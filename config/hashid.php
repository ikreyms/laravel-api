<?php

return [
    'field' => 'hashid',
    'salt' => env('HASHID_SALT', 'hellothere'),
    'length' => 10,
    'chars' => 'abcdefghijklmnopqrstuvwxyz'
];
