<?php

use App\Entity\Account;
use App\Entity\EntityException;
use App\Entity\EntryDetail;
use App\Entity\Oversight;
use App\Entity\OversightEntry;
use App\Entity\Parameter;
use PHPUnit\Framework\TestCase;

class EntityTest extends TestCase {

    public function testSomething() {

        //$account = new Account("Gimmy", "Razafimbelo", "gimmyarazafimbelo2@gmail.com", "mdpDeGimmy", 1);
        //$oversight = new Oversight(1, "2022-11-07 16:21:00", "Syndrome néphrotique", 1);
        //$parameter = new Parameter(1, "Tension", "", 1);
        //$oversightEntry = new OversightEntry(1, "2022-11-07 16:21:00", 1);
        //$entryDetail = new EntryDetail(1, 1, 112, 1);

        try {

            //$account->validate();
            //$oversight->validate();
            //$parameter->validate();
            //$oversightEntry->validate();
            //$entryDetail->validate();

        } catch(EntityException $e) {

            die('Error: '. $e->message);

        }

    }

}

?>