<?php

namespace core\models;

use core\engine\Application;
use core\engine\Configuration;

class Coin
{
    const COMMON_COIN = 'ETH';

    static public function coins($onlyActivate = true)
    {
        $coins = [];
        foreach (Configuration::$CONFIG['coins'] as $coin => $data) {
            if (!$onlyActivate || $data['activate']) {
                $coins[] = $coin;
            }
        }
        return $coins;
    }

    static public function token()
    {
        return Configuration::$CONFIG['token']['name'];
    }

    const RATE_KEY_PREFIX = 'rate_count_of_usd_in_';

    static public function setRate($coin, $rate)
    {
        $coin = strtoupper($coin);
        Application::setValue(self::RATE_KEY_PREFIX . $coin, $rate);
    }

    static public function getRate($coin)
    {
        $coin = strtoupper($coin);
        return Application::getValue(self::RATE_KEY_PREFIX . $coin);
    }

    static public function issetCoin($coin)
    {
        return isset(Configuration::$CONFIG['coins'][$coin]);
    }

    static public function checkDepositConfirmation($coin, $conf)
    {
        if (!Coin::issetCoin($coin)) {
            return false;
        }
        return $conf >= Configuration::$CONFIG['coins'][$coin]['min_conirmation'];
    }
}