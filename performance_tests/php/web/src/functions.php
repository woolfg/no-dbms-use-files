<?php

function getRandomEntryID() {
    return rand(1,CONFIG_NUMBER_ENTRIES);
}

function generateRandomIDList($count) {
    $ids = array();
    for($i=0;$i<$count;$i++) {
        $ids[] = getRandomEntryID();
    }
    return $ids;
}