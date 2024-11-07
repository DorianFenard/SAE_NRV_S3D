<?php

namespace nrv\nancy\repository;

use PDO;
use PDOException;

class NrvRepository
{
    private static $instance = null;
    private static $config;

    public static function setConfig($file)
    {
        self::$config = parse_ini_file($file);
        if (self::$config === false) {
            throw new PDOException("Erreur lors du chargement de la configuration.");
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            if (self::$config === null) {
                throw new PDOException("La configuration de la base de données n'est pas définie.");
            }

            $dsn = self::$config['driver'] . ':host=' . self::$config['host'] . ';dbname=' . self::$config['dbname'] . ';charset=utf8';
            self::$instance = new PDO($dsn, self::$config['username'], self::$config['password'], [
                PDO::ATTR_PERSISTENT => true,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_STRINGIFY_FETCHES => false
            ]);
        }
        return self::$instance;
    }
}