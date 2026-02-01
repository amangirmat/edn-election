<?php

return [
    [
        'name' => 'Election Center',
        'flag' => 'election.index',
    ],
    [
        'name'        => 'Create',
        'flag'        => 'election.create',
        'parent_flag' => 'election.index',
    ],
    [
        'name'        => 'Edit',
        'flag'        => 'election.edit',
        'parent_flag' => 'election.index',
    ],
    [
        'name'        => 'Delete',
        'flag'        => 'election.destroy',
        'parent_flag' => 'election.index',
    ],
];

return [
    [
        'name' => 'Chambers',
        'flag' => 'election.chambers.index',
    ],
    [
        'name' => 'Create',
        'flag' => 'election.chambers.create',
        'parent_flag' => 'election.chambers.index',
    ],
    [
        'name' => 'Edit',
        'flag' => 'election.chambers.edit',
        'parent_flag' => 'election.chambers.index',
    ],
    [
        'name' => 'Delete',
        'flag' => 'election.chambers.destroy',
        'parent_flag' => 'election.chambers.index',
    ],
];