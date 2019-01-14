<?php

function checkEssentialInputs($method, $inputs) {
    foreach ($inputs as $in) {
        if (!isset($method[$in])) {
            return FALSE;
        }
    }
    return TRUE;
}

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

