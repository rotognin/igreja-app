<?php

namespace App\Workflow\Relatorio;

use App\SGC\DAO\Usuario;
use App\Workflow\DAO\Passo as PassoDAO;
use App\Workflow\DAO\Workflow as WorkflowDAO;
use App\CADASTRO\DAO\Fornecedor;
use App\CADASTRO\DAO\Endereco;
use Funcoes\Helpers\Format;
use Funcoes\Lib\Email\Email;
use App\SGC\DAO\Email as EmailDAO;

class RelatorioEmail
{
    private bool $okEnviar = true;
    private array $arrayEmails = array();

    private array $arrayTipoRelatorio = array(
        'aprovação_gerente_compras' => 'Aprovação Compras',
        'aprovação_gerente_financeiro' => 'Aprovação Financeiro'
    );

    // DAO
    private $usuarioDAO;
    private $passoDAO;
    private $workflowDAO;
    private $fornecedorDAO;
    private $enderecoDAO;
    private $emailDAO;

    public function __construct(array $registros)
    {
        $this->usuarioDAO = new Usuario();
        $this->passoDAO = new PassoDAO();
        $this->workflowDAO = new WorkflowDAO();
        $this->fornecedorDAO = new Fornecedor();
        $this->enderecoDAO = new Endereco();
        $this->emailDAO = new EmailDAO();

        $this->arrayEmails = $this->prepararEnvios($registros);
    }

    private function prepararEnvios(array $registros)
    {
        $existemRegistros = false;

        // Usar o array passado para adicionar o destinatário, o assunto e o corpo do e-mail
        foreach ($registros as $key => $registro) {
            // Checar se tem pendências (Passos em aberto a serem verificados para esse usuário)
            $pendencias = array();
            $pendencias = $this->buscarPendencias($registro['email_tipo_relatorio']);

            if (!empty($pendencias)) {
                // Destinatário do e-mail
                [$destino, $nome] = $this->buscarDestino($registro['email_usuario']);

                if (filter_var($destino, FILTER_VALIDATE_EMAIL)) {
                    $registros[$key]['destino'] = $destino;
                }

                // Assunto do e-mail
                $assunto = $this->buscarAssunto($registro['email_tipo_relatorio']);
                $registros[$key]['assunto'] = $assunto;

                // Mensagem (corpo do e-mail)
                $mensagem = $this->montarMensagem($registro['email_tipo_relatorio'], $nome, $pendencias);
                $registros[$key]['mensagem'] = $mensagem;

                $existemRegistros = true;
            } else {
                unset($registros[$key]);
            }
        }

        $this->okEnviar = $existemRegistros;

        return $registros;
    }

    private function buscarPendencias(string $tipoRelatorio): array
    {
        // Lógica principal para montagem do array com as informações
        $pendencias = array();

        // Buscar passos que estão em aberto para aprovação
        $where = array('');
        $where[0] = ' AND pas_titulo = ? AND pas_status = ?';
        $where[1][] = $this->arrayTipoRelatorio[$tipoRelatorio];
        $where[1][] = 0;
        $arrayPassos = $this->passoDAO->getArray($where);

        // Ler as informações do Workflow para cada passo em aberto desse tipo
        if (!empty($arrayPassos)) {
            foreach ($arrayPassos as $passo) {
                $workflow = $this->workflowDAO->get($passo['pas_workflow_id']);
                $fornecedor = $this->fornecedorDAO->get($workflow['wrk_entidade_id']);

                [$cidade, $uf] = $this->buscarEndereco('fornecedor', $fornecedor['for_id']);

                $pendencias[] = array(
                    'fornecedor' => $fornecedor['for_razao'],
                    'documento' => ($fornecedor['for_pessoa'] == 'PF') ? Format::cpf($fornecedor['for_documento']) : Format::cnpj($fornecedor['for_documento']),
                    'cidade' => $cidade,
                    'uf' => $uf
                );
            }
        }

        return $pendencias;
    }

    private function buscarEndereco(string $entidade, int $entidade_id): array
    {
        $cidade = '';
        $uf = '';

        $where = array('');
        $where[0] = ' AND end_entidade = ? AND end_entidade_id = ?';
        $where[1][] = $entidade;
        $where[1][] = $entidade_id;

        $arrayEnderecos = $this->enderecoDAO->getArray($where, 'end_id ASC');

        if (!empty($arrayEnderecos)) {
            // Pegar apenas o primeiro endereço
            $cidade = $arrayEnderecos[0]['end_cidade'];
            $uf = $arrayEnderecos[0]['end_estado'];
        }

        return [$cidade, $uf];
    }

    private function buscarDestino(string $usuario): array
    {
        $regUsuario = $this->usuarioDAO->get($usuario);
        return [$regUsuario['usu_email'], $regUsuario['usu_nome']];
    }

    private function buscarAssunto(string $tipoRelatorio): string
    {
        $assunto = '';

        switch ($tipoRelatorio) {
            case 'aprovação_gerente_compras':
                $assunto = 'Pendência de Aprovação - Gerente de Compras';
                break;
            case 'aprovação_gerente_financeiro':
                $assunto = 'Pendência de Aprovação - Gerente Financeiro';
                break;
        }

        return $assunto;
    }

    private function montarMensagem(string $tipoRelatorio, string $nome, array $pendencias): string
    {
        $mensagem = '';
        $corpo = '';

        $corpo = "
            <table style=\"border: 1px solid black\">
                <thead>
                    <tr>
                        <th style=\"border: 1px solid black\">Fornecedor</th>
                        <th style=\"border: 1px solid black\">Documento</th>
                        <th style=\"border: 1px solid black\">Cidade</th>
                        <th style=\"border: 1px solid black\">UF</th>
                    </tr>
                </thead>
                <tbody>
        ";

        foreach ($pendencias as $pendencia) {
            $corpo .= "
                <tr>
                    <td style=\"border: 1px solid black\">&nbsp;&nbsp;&nbsp;{$pendencia['fornecedor']}&nbsp;&nbsp;&nbsp;</td>
                    <td style=\"border: 1px solid black\">&nbsp;&nbsp;&nbsp;{$pendencia['documento']}&nbsp;&nbsp;&nbsp;</td>
                    <td style=\"border: 1px solid black\">&nbsp;&nbsp;&nbsp;{$pendencia['cidade']}&nbsp;&nbsp;&nbsp;</td>
                    <td style=\"border: 1px solid black\">&nbsp;&nbsp;&nbsp;{$pendencia['uf']}&nbsp;&nbsp;&nbsp;</td>
                </tr>
            ";
        }

        $corpo .= "
                </tbody>
            </table>
        ";

        switch ($tipoRelatorio) {
            case 'aprovação_gerente_compras':
                $mensagem = "
                    <h3>{$nome}</h3>
                    <p>Segue abaixo uma lista de Fornecedores que estão Pendentes de serem aprovados pelo Gerente de Compras:</p>
                    <p>{$corpo}</p>
                ";
                break;

            case 'aprovação_gerente_financeiro':
                $mensagem = "
                    <h3>{$nome}</h3>
                    <p>Segue abaixo uma lista de Fornecedores que estão Pendentes de serem aprovados pelo Gerente Financeiro:</p>
                    <p>{$corpo}</p>
                ";
                break;
        }

        return $mensagem;
    }

    public function okEnvio(): bool
    {
        return $this->okEnviar;
    }

    private function erroEnvio(int $email_id, string $erroEnvio): void
    {
        $this->emailDAO->update($email_id, [
            'email_ultimo_envio' => date('Y-m-d H:i:s'),
            'email_erro_envio' => $erroEnvio
        ]);
    }

    private function podeEnviar(array $email): bool
    {
        if ($email['destino'] == '') {
            $this->erroEnvio($email['email_id'], 'Destinatário não informado');
            return false;
        }

        if ($email['assunto'] == '') {
            $this->erroEnvio($email['email_id'], 'Assunto do e-mail em branco');
            return false;
        }

        if ($email['mensagem'] == '') {
            $this->erroEnvio($email['email_id'], 'Corpo do e-mail em branco');
            return false;
        }

        return true;
    }

    private function sucessoEnvio(int $email_id): void
    {
        $this->emailDAO->update($email_id, [
            'email_ultimo_envio' => date('Y-m-d H:i:s'),
            'email_erro_envio' => ''
        ]);
    }

    public function enviar(string $ambiente): bool
    {
        $emailEnvio = new Email($ambiente);

        if (!empty($this->arrayEmails)) {
            foreach ($this->arrayEmails as $email) {
                if ($this->podeEnviar($email)) {
                    $retornoEnvio = '';

                    $emailEnvio->setDestino($email['destino']);
                    $emailEnvio->setAssunto($email['assunto']);
                    $emailEnvio->setMensagem($email['mensagem']);
                    $retornoEnvio = $emailEnvio->enviar();

                    ($retornoEnvio == '') ?
                        $this->sucessoEnvio($email['email_id']) :
                        $this->erroEnvio($email['email_id'], $retornoEnvio);
                }
            }
        }

        return true;
    }
}
