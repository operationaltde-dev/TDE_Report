<?php

return [
    'pdf' => [
        'enabled' => true,
        'binary' => 'C:\Program Files\wkhtmltopdf\bin',
        'options' => [
            'page-size' => 'A4',
            'orientation' => 'Landscape',
            'margin-top' => '10mm',
            'margin-bottom' => '15mm',
            'margin-left' => '10mm',
            'margin-right' => '10mm',
            'encoding' => 'UTF-8',
            'enable-local-file-access' => true,
            'no-outline' => true,
        ],
    ],
];
