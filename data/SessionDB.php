<?php

require_once('../model/Session.php');
require_once('../model/User.php');

class SessionDB
{
    private $database;

    public function __construct($database)
    {
        $this->database =  $database;
    }

    public function insertar($session)
    {
        $USE_ID = $session->getUseId();
        $Acctok = $session->getAcctok();
        $Acctokexp = $session->getAcctokexp();
        $Reftok = $session->getReftok();
        $Reftokexp = $session->getReftokexp();
        $query = $this->database->prepare('INSERT INTO SESSIONS(USE_ID, SES_ACCTOK, SES_ACCTOKEXP, SES_REFTOK, SES_REFTOKEXP) VALUES (?, ?, STR_TO_DATE(?, "%d/%m/%Y %H:%i"), ?, STR_TO_DATE(?, "%d/%m/%Y %H:%i"))');
        $query->bindParam(1, $USE_ID, PDO::PARAM_INT);
        $query->bindParam(2, $Acctok, PDO::PARAM_STR);
        $query->bindParam(3, $Acctokexp, PDO::PARAM_STR);
        $query->bindParam(4, $Reftok, PDO::PARAM_STR);
        $query->bindParam(5, $Reftokexp, PDO::PARAM_STR);
        $query->execute();

        $rowCount = $query->rowCount();

        return $rowCount;
    }

    public function reemplazar($session, $USE_ID)
    {
        $SES_ID = $session->getId();
        $Acctok = $session->getAcctok();
        $Acctokexp = $session->getAcctokexp();
        $Reftok = $session->getReftok();
        $Reftokexp = $session->getReftokexp();
        $query = $this->database->prepare('UPDATE SESSIONS SET SES_ACCTOK = ?,  SES_ACCTOKEXP = STR_TO_DATE(?, "%d/%m/%Y %H:%i"),  SES_REFTOK = ?, SES_REFTOKEXP = STR_TO_DATE(?, "%d/%m/%Y %H:%i") WHERE SES_ID = ? AND USE_ID = ?');
        $query->bindParam(1, $Acctok, PDO::PARAM_STR);
        $query->bindParam(2, $Acctokexp, PDO::PARAM_STR);
        $query->bindParam(3, $Reftok, PDO::PARAM_STR);
        $query->bindParam(4, $Reftokexp, PDO::PARAM_STR);
        $query->bindParam(5, $SES_ID, PDO::PARAM_INT);
        $query->bindParam(6, $USE_ID, PDO::PARAM_INT);
        $query->execute();

        $rowCount = $query->rowCount();

        return $rowCount;
    }

    public function obtenerPorId($SES_ID, $USE_ID)
    {
        $query = $this->database->prepare('SELECT SES_ID, USE_ID, SES_ACCTOK, DATE_FORMAT(SES_ACCTOKEXP, "%d/%m/%Y %H:%i") AS SES_ACCTOKEXP, SES_REFTOK, DATE_FORMAT(SES_REFTOKEXP, "%d/%m/%Y %H:%i") AS SES_REFTOKEXP  FROM SESSIONS WHERE SES_ID = ? AND USE_ID = ?');
        $query->bindParam(1, $SES_ID, PDO::PARAM_INT);
        $query->bindParam(2, $USE_ID, PDO::PARAM_INT);
        $query->execute();

        $sessionArray = array();

        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $session = new Session($row['SES_ID'], $row['USE_ID'], $row['SES_ACCTOK'], $row['SES_ACCTOKEXP'], $row['SES_REFTOK'], $row['SES_REFTOKEXP']);

            $sessionArray[] = $session->returnSessionAsArray();
        }

        return $sessionArray;
    }

    public function obtenerUserPorSession($SES_ID, $SES_ACCTOK, $SES_REFTOK)
    {
        $query = $this->database->prepare('SELECT S.SES_ID, U.USE_ID, S.SES_ACCTOK, DATE_FORMAT(S.SES_ACCTOKEXP, "%d/%m/%Y %H:%i") AS SES_ACCTOKEXP, S.SES_REFTOK, DATE_FORMAT(S.SES_REFTOKEXP, "%d/%m/%Y %H:%i") AS SES_REFTOKEXP, U.USE_ACTIVE, U.USE_LOGAT  FROM SESSIONS S INNER JOIN USERS U ON S.USE_ID = U.USE_ID WHERE S.SES_ID = ? AND S.SES_ACCTOK = ? AND S.SES_REFTOK = ? ');
        $query->bindParam(1, $SES_ID, PDO::PARAM_INT);
        $query->bindParam(2, $SES_ACCTOK, PDO::PARAM_STR);
        $query->bindParam(3, $SES_REFTOK, PDO::PARAM_STR);
        $query->execute();

        $sessionArray = array();
        $userArray = array();

        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $session = new Session($row['SES_ID'], $row['USE_ID'], $row['SES_ACCTOK'], $row['SES_ACCTOKEXP'], $row['SES_REFTOK'], $row['SES_REFTOKEXP']);
            $user = new User($row['USE_ID'], null, null, null, $row['USE_ACTIVE'], $row['USE_LOGAT']);

            $sessionArray[] = $session->returnSessionAsArray();
            $userArray[] = $user->returnUserAsArray();
        }

        $returnData = array();
        if ($sessionArray != null) {
            $returnData['SESSION'] = $sessionArray;
            $returnData['USER'] = $userArray;
        }

        return $returnData;
    }

    //Suficiente
    public function obtenerUserPorAccessToken($SES_ACCTOK)
    {
        $query = $this->database->prepare('SELECT S.SES_ID, U.USE_ID, S.SES_ACCTOK, DATE_FORMAT(S.SES_ACCTOKEXP, "%d/%m/%Y %H:%i") AS SES_ACCTOKEXP, S.SES_REFTOK, DATE_FORMAT(S.SES_REFTOKEXP, "%d/%m/%Y %H:%i") AS SES_REFTOKEXP, U.USE_ACTIVE, U.USE_LOGAT  FROM SESSIONS S INNER JOIN USERS U ON S.USE_ID = U.USE_ID WHERE S.SES_ACCTOK = ?');
        $query->bindParam(1, $SES_ACCTOK, PDO::PARAM_STR);
        $query->execute();

        $sessionArray = array();
        $userArray = array();

        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            $session = new Session($row['SES_ID'], $row['USE_ID'], $row['SES_ACCTOK'], $row['SES_ACCTOKEXP'], $row['SES_REFTOK'], $row['SES_REFTOKEXP']);
            $user = new User($row['USE_ID'], null, null, null, $row['USE_ACTIVE'], $row['USE_LOGAT']);

            $sessionArray[] = $session->returnSessionAsArray();
            $userArray[] = $user->returnUserAsArray();
        }

        $returnData = array();

        $returnData['SESSION'] = $sessionArray;
        $returnData['USER'] = $userArray;

        return $returnData;
    }

    public function eliminarPorId($SES_ID)
    {
        $query = $this->database->prepare('DELETE FROM SESSIONS WHERE SES_ID = ?');
        $query->bindParam(1, $SES_ID, PDO::PARAM_INT);
        $query->execute();

        $rowCount = $query->rowCount();

        return $rowCount;
    }
}
