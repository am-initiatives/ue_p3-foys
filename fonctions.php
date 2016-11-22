<?php

function insert_operation($type,$numero_etage,$libelle,$description,$gadzarts_id,$montant,$date,$bdd)
{
    
    $req = $bdd->prepare('INSERT INTO operations(
                                    operation_id,
                                    operation_type,
                                    operation_etage,
                                    operation_libelle,
                                    operation_description,
                                    operation_gadzarts_id,
                                    operation_montant,
                                    operation_time) VALUES (NULL ,?,?,?,?,?,?,?)');
    $req->execute(array($type,$numero_etage,$libelle,$description,$gadzarts_id,$montant,$date));
    $req->closeCursor();

}

function debiter_gadz($montant,$gadz,$bdd)
{
    $nouveau_solde=$gadz['gadzarts_cachalot_solde']-abs($montant);
    $req = $bdd->prepare('UPDATE gadzarts SET gadzarts_cachalot_solde = ? WHERE gadzarts_id = ?');
    $req->execute(array($nouveau_solde,$gadz['gadzarts_id']));
    $req->closeCursor();
    
    // mise à jour de la date de négat'sss si passage en négat'sss (solde positif puis négatif)
    if($gadz['gadzarts_cachalot_solde']>=0 && $nouveau_solde<0)
    {
        $req = $bdd->prepare('UPDATE gadzarts SET gadzarts_cachalot_date_negats = ? WHERE gadzarts_id = ?');
        $req->execute(array(date("Y-m-d H:i:s"),$gadz['gadzarts_id']));
        $req->closeCursor();
    }
}
function crediter_gadz($montant,$gadz,$bdd)
{
    $nouveau_solde=$gadz['gadzarts_cachalot_solde']+abs($montant);
    $req = $bdd->prepare('UPDATE gadzarts SET gadzarts_cachalot_solde = ? WHERE gadzarts_id = ?');
    $req->execute(array($nouveau_solde,$gadz['gadzarts_id']));
    $req->closeCursor();
}
function crediter_gadz_alcool($volume_alcool_pur,$gadz,$bdd)
{
    $nouveau_volume=$gadz['gadzarts_cachalot_volume_alcool']+abs($volume_alcool_pur);
    $req = $bdd->prepare('UPDATE gadzarts SET gadzarts_cachalot_volume_alcool = ? WHERE gadzarts_id = ?');
    $req->execute(array($nouveau_volume,$gadz['gadzarts_id']));
    $req->closeCursor();
}
function debiter_gadz_alcool($volume_alcool_pur,$gadz,$bdd)
{
    $nouveau_volume=$gadz['gadzarts_cachalot_volume_alcool']-abs($volume_alcool_pur);
    $req = $bdd->prepare('UPDATE gadzarts SET gadzarts_cachalot_volume_alcool = ? WHERE gadzarts_id = ?');
    $req->execute(array($nouveau_volume,$gadz['gadzarts_id']));
    $req->closeCursor();
}
function debiter_etage($montant,$etage,$bdd)
{
    $nouveau_solde=$etage['solde']-abs($montant);
    $req = $bdd->prepare('UPDATE etages SET solde = ? WHERE etage = ?');
    $req->execute(array($nouveau_solde,$etage['etage']));
    $req->closeCursor();
}
function crediter_etage($montant,$etage,$bdd)
{
    $nouveau_solde=$etage['solde']+abs($montant);
    $req = $bdd->prepare('UPDATE etages SET solde = ? WHERE etage = ?');
    $req->execute(array($nouveau_solde,$etage['etage']));
    $req->closeCursor();
}

function annuler_operation($id_operation)
{
    /*
     * Récupérer les caractéristiques de l'opération
     * Recréditer gadz
     * Faire la balance
     * rajouter une ligne d'opération
     * 
     */
}
    /*
    if(($gadz['gadzarts_cachalot_solde']+$montant)<0 && $gadz['gadzarts_cachalot_solde']>=0) // passage en négat'sss
    {
            $query = $this->db->query("UPDATE gadzarts SET gadzarts_cachalot_solde ='".($user['gadzarts_cachalot_solde']+$montant)."', gadzarts_cachalot_date_negats=NOW( ), gadzarts_cachalot_volume_alcool='".($user['gadzarts_cachalot_volume_alcool']+$volume_alcool_de_la_commande)."' WHERE  gadzarts_id ='".$_POST['gadzarts_id']."'");
    }
    else
    {
            $query = $this->db->query("UPDATE gadzarts SET gadzarts_cachalot_solde ='".($user['gadzarts_cachalot_solde']+$montant)."', gadzarts_cachalot_volume_alcool='".($user['gadzarts_cachalot_volume_alcool']+$volume_alcool_de_la_commande)."' WHERE  gadzarts_id ='".$_POST['gadzarts_id']."'");
    }
    */
    