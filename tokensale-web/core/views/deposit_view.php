<?php

namespace core\views;

use core\controllers\Deposit_controller;
use core\engine\Application;
use core\engine\DB;
use core\models\Deposit;

class Deposit_view
{
    static public function setMinimalsForm()
    {
        ob_start();
        ?>
        <div class="row card administrator-settings-block">
            <form class="registration col s12" action="<?= Deposit_controller::TOKENS_SET_URL ?>" method="post">
                <h5 class="center">Set minimal values</h5>
                <label>Minimal tokens for minting:</label>
                <input type="number"
                       name="<?= Deposit::MINIMAL_TOKENS_FOR_MINTING_KEY ?>" placeholder="1"
                       value="<?= Deposit::minimalTokensForMinting() ?>"
                       min="0" max="9999999999" step="0.00000001">
                <label>Minimal tokens for bounty:</label>
                <input type="number"
                       name="<?= Deposit::MINIMAL_TOKENS_FOR_BOUNTY_KEY ?>" placeholder="1"
                       value="<?= Deposit::minimalTokensForBounty() ?>"
                       min="0" max="9999999999" step="0.00000001">
                <div class="row center">
                    <button type="submit" class="waves-effect waves-light btn btn-send" style="width: 100%">
                        Set
                    </button>
                </div>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }

    static public function setPersmissionsForm()
    {
        ob_start();
        ?>
        <div class="row card administrator-settings-block">
            <form class="registration col s12" action="<?= Deposit_controller::PERMISSIONS_SET_URL ?>" method="post">
                <h5 class="center">Set permissions values</h5>
                <label>Receiving deposits is on:</label>
                <input type="number"
                       name="<?= Deposit::RECEIVING_DEPOSITS_IS_ON ?>" placeholder="1"
                       value="<?= Deposit::receivingDepositsIsOn() ?>"
                       min="0" max="1" step="1">
                <label>Minting is on:</label>
                <input type="number"
                       name="<?= Deposit::MINTING_IS_ON ?>" placeholder="1"
                       value="<?= Deposit::mintingIsOn() ?>"
                       min="0" max="1" step="1">
                <div class="row center">
                    <button type="submit" class="waves-effect waves-light btn btn-send" style="width: 100%">
                        Set
                    </button>
                </div>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }

    static public function transactions()
    {
        ob_start() ?>

        <div class="container">
            <?php
            $deposits = Deposit::investorDeposits(Application::$authorizedInvestor->id);
            if (!$deposits) {
                ?>
                <h3>There is no transaction yet</h3>
                <?= Wallet_view::newContribution() ?>
                <?php
            } else {
                ?>
                <div class="head-collapsible">
                    <div class="row">
                        <div class="col s1 collapsible-col center">Type</div>
                        <div class="col s4 collapsible-col">Date</div>
                        <div class="col s4 collapsible-col">Description</div>
                        <div class="col s2 collapsible-col">Amount</div>
                        <div class="col s1 collapsible-col"></div>
                    </div>
                </div>
                <ul class="collapsible" data-collapsible="accordion">
                    <?php
                    foreach ($deposits as $deposit) {
                        ?>
                        <li>
                            <div class="collapsible-header">
                                <div class="row">
                                    <div class="col s1 collapsible-col center">
                                        Send
                                    </div>
                                    <div class="col s4 collapsible-col">
                                        <?= DB::timetostr($deposit->datetime) ?>
                                    </div>
                                    <div class="col s4 collapsible-col">
                                        <?php
                                        if ($deposit->is_donation) {
                                            echo 'Transferred as donation';
                                        } else {
                                            if ($deposit->used_in_bounty) {
                                                echo 'Used in bounty';
                                            } else {
                                                echo 'Not used in bounty';
                                            }
                                            echo '<br>';
                                            if ($deposit->used_in_minting) {
                                                echo 'Used in minting';
                                            } else {
                                                echo 'Not used in minting';
                                            }
                                        }
                                        ?>
                                    </div>
                                    <div class="col s2 collapsible-col">
                                        <?= $deposit->amount ?> <?= $deposit->coin ?>
                                    </div>
                                    <div class="col s1 collapsible-col">
                                        <i class="material-icons">keyboard_arrow_right</i>
                                    </div>
                                </div>
                            </div>
                            <div class="collapsible-body">
                                <div class="row">
                                    <div class="col s1"></div>
                                    <div class="col s10">
                                        <p>Converted to <?= $deposit->usd ?> USD</p>
                                        <p>Order Id:#<?= $deposit->id ?></p>
                                        <p>txid: <?= $deposit->txid ?></p>
                                        <p>vout: <?= $deposit->vout ?></p>
                                    </div>
                                    <div class="col s1"></div>
                                </div>
                            </div>
                        </li>
                    <?php } ?>
                </ul>
            <?php } ?>
        </div>

        <?php
        return ob_get_clean();
    }
}