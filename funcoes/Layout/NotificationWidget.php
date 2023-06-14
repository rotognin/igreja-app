<?php

namespace Funcoes\Layout;

class NotificationWidget
{
    public static function render()
    {
        return <<<HTML
        <li class="nav-item dropdown dropdown-notificacao">
            <a class="nav-link" href="#" data-toggle="dropdown" role="button">
                <i class="far fa-bell"></i>
                <span class="badge badge-danger navbar-badge">0</span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <h6 class="dropdown-header">Notificações não lidas <span class="dropdown-notificacao-count"></span></h6>
                <div class="dropdown-divider"></div>
                <div class="dropdown-item empty-notificacao">
                    <p class="text-center text-muted text-xs">Todas as notificações foram lidas</p>
                </div>
                <div class="dropdown-notificacao-container">

                </div>
                <div class="dropdown-divider"></div>
                <a href="/geral/notificacoes.php" class="dropdown-footer">Ver todas as notificações</a>
            </div>
        </li>
        <script>
            $(function() {
                startupNotificacao();
            });
            function startupNotificacao() {
                $('.dropdown-notificacao .badge').hide();
                fetchNotificacoes();
                setInterval(() => {
                    fetchNotificacoes();
                }, 30000);
            }

            function fetchNotificacoes() {
                const badge = $('.dropdown-notificacao .badge');
                const container = $('.dropdown-notificacao-container');
                const empty = $('.empty-notificacao');
                $.get('/geral/xhr/fetchNotificacoesNaoLidas.php').then(function(data) {
                    if (data.length == 0) {
                        badge.hide();
                        empty.show();
                        container.find('*').remove();
                        $('.dropdown-notificacao-count').text('');
                    } else {
                        let count = data.length;
                        count = count > 9 ? '9+' : count;
                        badge.text(count);
                        badge.show('fast');
                        empty.hide();
                        container.find('*').remove();
                        $('.dropdown-notificacao-count').text('(' + data.length + ')');
                        data.forEach(function(notificacao) {
                            container.append(`
                            <a href="/geral/lerNotificacao.php?not_id=\${notificacao.not_id}" onclick="clickNotificacao(\${notificacao.not_id}, this)" class="dropdown-item">
                                <p class="text-sm mb-2"><i class="\${notificacao . not_icone}"></i> \${notificacao . not_mensagem}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <button class="btn-xs btn btn-outline-primary" onclick="lerNotificacao(\${notificacao.not_id}, this, event)">
                                            <i class="fas fa-check mr-1"></i>
                                            Marcar como lida
                                        </button>
                                    </div>
                                    <div class="text-muted text-xs">
                                        <i class="far fa-clock mr-1"></i>
                                        \${moment(notificacao . not_data_inc) . format('DD/MM/YYYY HH:mm')}
                                    </div>
                                </div>
                            </a>
                            `);
                        });
                    }
                });
            }

            function lerNotificacao(not_id, btn, event) {
                if (event) {
                    event.stopPropagation();
                    event.preventDefault();
                }
                $.get(`/geral/xhr/lerNotificacao.php?not_id=\${not_id}`);
                $(btn).parents('.dropdown-item').hide('fast', function() {
                    $(this).remove();
                    const len = $('.dropdown-notificacao-container .dropdown-item').length;
                    if (len == 0) {
                        $('.dropdown-notificacao .badge').hide('fast');
                        $('.dropdown-notificacao-count').text('');
                        $('.empty-notificacao').show('fast');
                    } else {
                        $('.dropdown-notificacao .badge').text(len > 9 ? '9+' : len);
                        $('.dropdown-notificacao-count').text('(' + len + ')');
                    }
                });
                return false;
            }

            function clickNotificacao(not_id, a) {
                lerNotificacao(not_id, $(a).find('button'));
            }
        </script>
        HTML;
    }
}
