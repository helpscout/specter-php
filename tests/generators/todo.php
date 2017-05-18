<?php
/**
 * Fixture configuration for Todos
 */
return [
    'outputDirectory' => './examples/fixtures',
    'specterDirectory' => './tests/fixture',
    'specs' => [
        [
            'specterFile' => 'todo.json',
            'outputFile'  => 'todo-simple.json',
            'seed'        => 1,
            'description' => 'A simple todo example',
        ],
        [
            'specterFile' => 'todo.json',
            'outputFile'  => 'todo-no-title.json',
            'seed'        => 2,
            'postProcess' => function ($fixture) {
                $fixture->title = '';
            },
            'description' => 'A todo without a title',
        ],
        [
            'specterFile' => 'todo.json',
            'outputFile'  => 'todo-with-integer-id.json',
            'seed'        => 3,
            'postProcess' => function ($fixture) {
                $fixture->id = 45678;
            },
            'description' => 'A todo with an integer id instead of a uuid',
        ]
    ]
];
