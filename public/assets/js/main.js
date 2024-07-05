const datarangepickerLocale = {
    "separator": " - ",
    "applyLabel": "OK",
    "cancelLabel": "Cancelar",
    "fromLabel": "De",
    "toLabel": "Até",
    "customRangeLabel": "Personalizado",
    "weekLabel": "Sem",
    "daysOfWeek": [
        "Dom",
        "Seg",
        "Ter",
        "Qua",
        "Qui",
        "Sex",
        "Sab"
    ],
    "monthNames": [
        "Janeiro",
        "Fevereiro",
        "Março",
        "Abril",
        "Maio",
        "Junho",
        "Julho",
        "Agosto",
        "Setembro",
        "Outubro",
        "Novembro",
        "Dezembro"
    ],
    "firstDay": 1,
};
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    timer: 6000,
    showConfirmButton: false,
    timerProgressBar: true,
    showCloseButton: true,
});
let toasts = [];
$(function () {
    toasts.map((toast) => {
        Toast.fire(toast);
    });

    $.fn.select2.defaults.set("language", {
        errorLoading: function () {
            return "Os resultados não puderam ser carregados.";
        },
        inputTooLong: function (e) {
            var t = e.input.length - e.maximum,
                n = "Por favor, apague " + t + " caracter";
            return t != 1 && (n += "es"), n;
        },
        inputTooShort: function (e) {
            var t = e.minimum - e.input.length,
                n = "Por favor, insira " + t + " ou mais caracteres";
            return n;
        },
        loadingMore: function () {
            return "Carregando mais resultados…";
        },
        maximumSelected: function (e) {
            var t = "Você só pode selecionar " + e.maximum + " ite";
            return e.maximum == 1 ? t += "m" : t += "ns", t;
        },
        noResults: function () {
            return "Nenhum resultado encontrado";
        },
        searching: function () {
            return "Buscando…";
        },
    });

    $.fn.select2.defaults.set("theme", "bootstrap4");

    $.applyDataMask();

    $.validator.setDefaults({
        errorElement: 'span',
        errorPlacement: function (error, element) {
            error.addClass('invalid-feedback');
            element.closest('.form-group').append(error);
        },
        highlight: function (element, errorClass, validClass) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass('is-invalid');
        }
    });

    $('.nav .nav-link.active').parent().parents('.nav-item').addClass('menu-open').each(function () {
        $(this).find('.nav-link:first').addClass('active');
    });
});

function confirm(text, confirmButtonText = 'Sim', cancelButtonText = 'Não') {
    return Swal.fire({
        title: 'Confirmação',
        text: text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#007bff',
        cancelButtonColor: '#d33',
        confirmButtonText: confirmButtonText,
        cancelButtonText: cancelButtonText,
    });
};

function mensagem(text, confirmButtonText = 'OK') {
    return Swal.fire({
        title: 'Atenção',
        text: text,
        icon: 'warning',
        showCancelButton: false,
        confirmButtonColor: '#007bff',
        cancelButtonColor: '#d33',
        confirmButtonText: confirmButtonText
    });
};

function validar_cnpj(cnpj) {
    function getVerificationCode1() {
        var total = 0;
        var mod = 0;
        var factors = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        var nums = this.cnpj.substr(0, 12).split('');
        for (var i in nums) {
            total += nums[i] * factors[i];
        }
        mod = total % 11;
        return (mod < 2) ? 0 : 11 - mod;
    }

    function getVerificationCode2(code1) {
        var total = 0;
        var mod = 0;
        var factors = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        var nums = (this.cnpj.substr(0, 12) + code1).split('');
        for (var i in nums) {
            total += nums[i] * factors[i];
        }
        mod = total % 11;
        return (mod < 2) ? 0 : 11 - mod;
    }

    this.cnpj = cnpj.replace(/[^0-9]/g, '');
    this.verificationCode1;
    this.verificationCode2;

    this.verificationCode1 = this.cnpj.substr(-2, 1);
    this.verificationCode2 = this.cnpj.substr(-1, 1);

    var code1 = getVerificationCode1();
    var code2 = getVerificationCode2(code1);

    return ((code1 == this.verificationCode1) && (code2 == this.verificationCode2));
};

function validar_cpf(cpf) {
    var Soma;
    var Resto;
    Soma = 0;

    if (cpf == "00000000000") return false;

    for (i = 1; i <= 9; i++) {
        Soma = Soma + parseInt(cpf.substring(i - 1, i)) * (11 - i)
    };
    Resto = (Soma * 10) % 11;

    if ((Resto == 10) || (Resto == 11)) {
        Resto = 0;
    }

    if (Resto != parseInt(cpf.substring(9, 10))) {
        return false;
    }

    Soma = 0;
    for (i = 1; i <= 10; i++) {
        Soma = Soma + parseInt(cpf.substring(i - 1, i)) * (12 - i);
    }
    Resto = (Soma * 10) % 11;

    if ((Resto == 10) || (Resto == 11)) {
        Resto = 0;
    }
    if (Resto != parseInt(cpf.substring(10, 11))) {
        return false;
    }

    return true;
}

function formatar_cnpj(valor) {
    valor = valor.replace(/\D/g, '');
    valor = valor.replace(/(\d{2})(\d)/, '$1.$2');
    valor = valor.replace(/(\d{3})(\d)/, '$1.$2');
    valor = valor.replace(/(\d{3})(\d)/, '$1/$2');
    valor = valor.replace(/(\d{4})(\d)/, '$1-$2');
    return valor;
}

function formatar_cpf(valor) {
    valor = valor.replace(/\D/g, '');
    valor = valor.replace(/(\d{3})(\d)/, '$1.$2');
    valor = valor.replace(/(\d{3})(\d)/, '$1.$2');
    valor = valor.replace(/(\d{3})(\d)/, '$1-$2');
    return valor;
}

function formatar_cep(valor) {
    valor = valor.toString();
    valor = valor.replace(/\D/g, '');
    valor = valor.replace(/(\d{5})(\d)/, '$1-$2')
    valor = valor.replace(/(-\d{3})\d+$/, '$1');
    return valor;
}

function consultar_cep(cep) {
    cep = cep.replace(/\D/g, '');
    if (cep.length != 8) {
        return false;
    }

    $.ajax({ url: 'geral/endereco/consulta.php?cep=' + cep })
        .done(function (data) {
            var json = data.slice(39);
            var dados = JSON.parse(json);

            // Checar se deu erro
            if ('erro' in dados) {
                alert('Erro: ' + dados.mensagem);
                return false;
            }

            if (dados.cidade == '') {
                return false;
            }

            // Preencher os campos
            $("#end_logradouro").val(dados.logradouro);
            $("#end_complemento").val(dados.complemento);
            $("#end_bairro").val(dados.bairro);
            $("#end_cidade").val(dados.cidade);
            $("#end_estado").val(dados.estado);
            $("#end_pais").val(dados.pais);
            $('input[name="end_cidade_ibge"]').val(dados.ibge);
            $('input[name="end_cidade_gia"]').val(dados.gia);

            if (dados.logradouro !== '') {
                $("#end_numero").focus();
            }
        });
}

function consultarTipoDocumento(tipo) {
    if (isNaN(tipo)) {
        return false;
    }

    // Fazer a consulta pelo Ajax
    var frase = '<i>Aguarde...</i>';
    $("#info_arquivo").html(frase);

    $.ajax({ url: '../xhr.php?c1=cadastro&c2=geral&c3=documento&arquivo=consulta&tip_id=' + tipo })
        .done(function (data) {
            var dados = JSON.parse(data);

            // Checar se deu erro
            if ('erro' in dados) {
                alert('Erro: ' + dados.erro);
                return false;
            }

            var versionar = (dados.versionar == 'S') ? ' - Com versionamento.' : ' - Sem versionamento.';

            var frase = '';

            if (dados.extensoes !== '') {
                frase += 'Extensões permitidas: ' + dados.extensoes;
            }

            if (parseInt(dados.tamanho) > 0) {
                frase += '. Tamanho máximo: ' + dados.tamanho + ' MB';
            }

            frase += versionar;

            $("#info_arquivo").html(frase);
            $("input[name=doc_info_extensoes]").val(dados.extensoes);
            $("input[name=doc_info_tamanho]").val(dados.tamanho);
            $("input[name=doc_info_versionar]").val(dados.versionar);
        });
}

function buscarDocumento(doc_id = '') {
    if (doc_id == '') {
        return false;
    }

    const url = '../xhr.php?c1=cadastro&c2=geral&c3=documento&arquivo=abrir&doc_id=' + doc_id;

    window.open(url, '_blank').focus();
}

function buscarDescricaoCondicaoPagto(cond_id, campo) {
    var descricao = '';

    const url = '../xhr.php?c1=cadastro&c2=cadFornecedor&arquivo=consulta&cond_id=' + cond_id;

    $.ajax({ url })
        .done(function (data) {
            descricao = data;
            $('#' + campo).val(descricao);
        });
}

function buscarCidadesEstado(estado, campo, cidade_sel) {
    const url = '../../xhr.php?c1=cadastro&c2=parametros&c3=cadFrete&arquivo=consultaCidades&estado=' + estado;

    $.ajax({ url })
        .done(function (data) {
            const dados = JSON.parse(data);

            $('#' + campo).empty();

            if (Object.keys(dados).length == 0) {
                $('#' + campo).prop('disabled', true);
                return false;
            }

            var toAppend = '<option value="0" selected>&nbsp;</option>';

            $.each(dados, function (i, obj) {
                toAppend += '<option value=' + obj.id + '>' + obj.nome + '</option>';
            });

            $('#' + campo).append(toAppend).prop('disabled', false);

            if (cidade_sel > 0) {
                $('#' + campo).val(cidade_sel).change();
            }
        });
}

function displayOverlay(mensagem) {
    $("<table id='overlay'><tbody><tr><td>" + mensagem + "</td></tr></tbody></table>").css({
        "position": "fixed",
        "top": "0px",
        "left": "0px",
        "width": "100%",
        "height": "100%",
        "background-color": "rgba(0,0,0,.3)",
        "z-index": "10000",
        "vertical-align": "middle",
        "text-align": "center",
        "color": "#fff",
        "font-size": "40px",
        "font-weight": "bold",
        "cursor": "wait"
    }).appendTo("body");
}

/*
$(function () {
    $('form').submit(function () {
        displayOverlay('Aguarde...');
    });
});
*/

function dataValida(data) {
    if (data == '') {
        return true;
    }

    var arrData = data.split('/');
    if (arrData.length != 3) {
        return false;
    }

    var dia = arrData[0];
    var mes = arrData[1];
    var ano = arrData[2];

    if (dia > 31 || mes > 12 || ano > 2099) {
        return false;
    }

    if (mes == 4 || mes == 6 || mes == 9 || mes == 11) {
        if (dia > 30) {
            return false;
        }
    }

    if (mes == 2) {
        if ((ano % 4) != 0) {
            if (dia > 28) {
                return false;
            }
        }
    }

    return true;
}

// Comparar duas datas. A que deve ser menor e a que deve ser maior
// dd/mm/yyyy
function inicioMenor(menor, maior) {
    if (!dataValida(menor) || menor == '') {
        return false;
    }

    if (!dataValida(maior) || maior == '') {
        return false;
    }

    var arrDataMenor = menor.split('/');
    var arrDataMaior = maior.split('/');

    var dtMenor = arrDataMenor[2] + '-' + arrDataMenor[1] + '-' + arrDataMenor[0];
    var dtMaior = arrDataMaior[2] + '-' + arrDataMaior[1] + '-' + arrDataMaior[0];

    var dataMenor = new Date(dtMenor);
    var dataMaior = new Date(dtMaior);

    return (dataMenor <= dataMaior);
}

// Validação de Hora
function horaValida(hora, vazio = false) {
    if (hora == '') {
        return vazio;
    }

    var aHora = hora.split(":");
    if (aHora.length != 2) {
        return false;
    }

    var hh = parseInt(aHora[0]);
    var mm = parseInt(aHora[1]);

    if (hh > 23 || mm > 59) {
        return false;
    }

    return true;
}