<?php
//Desenvolvido por Rafael Tavares - Ibinetwork Informática Laravel DataBase
use WHMCS\Database\Capsule;
//Bloqueio de Acesso direto ao arquivo
if(!defined("WHMCS")){ die("Acesso restrito!");
}
function proximoDiaUtil($data, $saida = 'Y-m-d') {
    //Pegando Ano
    $ano = date('Y');
    //Lista de feriados nacionais
    $feriados = array(''.$ano.'-08-16', ''.$ano.'-01-25', ''.$ano.'-02-27', ''.$ano.'-02-28', ''.$ano.'-04-15', ''.$ano.'-04-21', ''.$ano.'-04-21', ''.$ano.'-05-01', ''.$ano.'-06-16', ''.$ano.'-07-09', ''.$ano.'-09-07', ''.$ano.'-10-12', 
    ''.$ano.'-11-02', ''.$ano.'-11-15', ''.$ano.'-12-25', ''.$ano.'-08-11');
    
	$i = 0; $diautil = date('Y-m-d', strtotime($data . ' +' . $i . ' Weekday')); while (in_array($diautil, $feriados)) { $i++; $diautil = date('Y-m-d', strtotime($data . ' +' . $i . ' Weekday'));
		}
		$timestamp_final = $diautil;
		//logActivity('[SEMANAL] Valor de diautil é '.$diautil.'.');
    
	return date($timestamp_final);
}
function dia_util($vars){
    //capturando o ID da fatura
    $id_invoice = $vars['invoiceid'];
    //Pega o vencimento da fatura
    foreach(Capsule::table('tblinvoices')->WHERE('id', $id_invoice)->get() as $invoicestbl){ $vencimentofatura = $invoicestbl->duedate;
    }
    //faz a verificação e a correção caso o vencimento seja em final de semana
    $verificao_vencimento = proximoDiaUtil($vencimentofatura);
    //Verifica se são diferentes, caso sim ele ira salvar a informação
    
    if(strtotime($vencimentofatura)!=strtotime($verificao_vencimento)){
        //faz update no banco de dados
        Capsule::table('tblinvoices')->WHERE('id', $id_invoice)->update(['duedate' => $verificao_vencimento]);
		//logActivity('[DIA UTIL] Valor de vencimentofatura '.$vencimentofatura.'.'); logActivity('[DIA UTIL] Valor de verificao_vencimento '.$verificao_vencimento.'.');
        logActivity('[DIA UTIL] A fatura N°'.$id_invoice.' foi alterada do dia '.$vencimentofatura.' para dia '.$verificao_vencimento.', pois o mesmo caia em um final de semana ou em um feriado nacional.');
    }
}
//Acionando o hook
add_hook('InvoiceCreationPreEmail',3,'dia_util');
?>
