<?php

function getRandomEntryID() {
    return random_int(1, CONFIG_NUMBER_ENTRIES);
}

function generateRandomIDList($count) {
    $ids = array();
    for($i=0;$i<$count;$i++) {
        $ids[] = getRandomEntryID();
    }
    return $ids;
}