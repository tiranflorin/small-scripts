<?php

$servername = "";
$username = "";
$password = "";
$dbName = '';
$fileName = 'Base de Datos Merged and Formatted.csv';

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbName", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "DB Connection failed: " . $e->getMessage();
    exit;
}

$now = new DateTime('now', new DateTimeZone('UTC'));
if (($handle = fopen($fileName, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

        // $data[0] -> affiliate_no
        // $data[1] -> name - surname
        // $data[3] -> amount

        $surname = substr($data[1], 0, strpos($data[1], ','));

        $select = "
            SELECT DISTINCT id, policy_amount, surname
            FROM dependants
            WHERE
             affiliate_no = ? OR
             surname LIKE '$surname%'";
        $stmt = $conn->prepare($select);
        $stmt->execute(array($data[0]));
        $results = $stmt->fetch();

        if (!empty($results)) {
            $amount = str_replace(',', '', $data[3]);
            if ($amount != $results['policy_amount']) {
                $update = "
                    UPDATE dependants
                    SET
                    policy_amount = :amount,
                    updated_at = :updated
                    WHERE id = :id";
                $stmt = $conn->prepare($update);
                $stmt->execute(array('amount' => $amount, 'updated' => $now->format('Y-m-d H:i:s'), 'id' => $results['id']));
            }
        }
    }
    fclose($handle);
}
