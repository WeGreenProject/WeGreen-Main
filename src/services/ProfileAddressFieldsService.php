<?php

class ProfileAddressFieldsService {
    public static function garantirCamposEnderecoPerfil($conn) {
        try {
            $temDistrito = false;
            $temLocalidade = false;
            $temCodigoPostal = false;

            $resultDistrito = $conn->query("SHOW COLUMNS FROM Utilizadores LIKE 'distrito'");
            if ($resultDistrito && $resultDistrito->num_rows > 0) {
                $temDistrito = true;
            }

            $resultLocalidade = $conn->query("SHOW COLUMNS FROM Utilizadores LIKE 'localidade'");
            if ($resultLocalidade && $resultLocalidade->num_rows > 0) {
                $temLocalidade = true;
            }

            $resultCodigoPostal = $conn->query("SHOW COLUMNS FROM Utilizadores LIKE 'codigo_postal'");
            if ($resultCodigoPostal && $resultCodigoPostal->num_rows > 0) {
                $temCodigoPostal = true;
            }

            if (!$temDistrito) {
                $conn->query("ALTER TABLE Utilizadores ADD COLUMN distrito VARCHAR(120) NULL AFTER morada");
            }

            if (!$temLocalidade) {
                $conn->query("ALTER TABLE Utilizadores ADD COLUMN localidade VARCHAR(120) NULL AFTER distrito");
            }

            if (!$temCodigoPostal) {
                $conn->query("ALTER TABLE Utilizadores ADD COLUMN codigo_postal VARCHAR(10) NULL AFTER localidade");
            }
        } catch (Exception $e) {
            
        }
    }
}
