<?php

if (!function_exists('formatarNome')) {
    function formatarNome($nome)
    {
        $nome = trim($nome);
        $nome = strtoupper($nome);

        return $nome;
    }
}
