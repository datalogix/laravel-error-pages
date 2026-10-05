<?php

return [
    'back_home' => 'Voltar ao início',
    'go_back' => 'Voltar e tentar novamente',
    'sign_in' => 'Entrar',

    400 => [
        'title' => 'Essa solicitação não funcionou muito bem',
        'description' => 'Algo nela não foi entendido. Você pode voltar e tentar novamente?',
    ],

    401 => [
        'title' => 'Faça login para continuar',
        'description' => 'Você precisa entrar para poder ver esta página.',
    ],

    403 => [
        'title' => 'Esta página não está disponível para você',
        'description' => 'Você não tem permissão para visualizar isso. Se achar que isso está errado, nos avise.',
    ],

    404 => [
        'title' => 'Não encontramos essa página',
        'description' => 'Ela pode ter sido movida, renomeada ou o link pode estar desatualizado.',
    ],

    419 => [
        'title' => 'Sua sessão expirou',
        'description' => 'Por segurança, desconectamos você após um tempo. Atualize a página e tente novamente.',
    ],

    429 => [
        'title' => 'Vá com calma',
        'description' => 'Você fez muitas solicitações em pouco tempo. Aguarde um momento e tente novamente.',
    ],

    500 => [
        'title' => 'Algo deu errado do nosso lado',
        'description' => 'Aconteceu algo inesperado do nosso lado. Tente novamente em instantes.',
    ],

    502 => [
        'title' => 'Estamos com problemas de conexão',
        'description' => 'Não conseguimos acessar nossos servidores agora. Tente novamente em breve.',
    ],

    503 => [
        'title' => 'Já voltamos',
        'description' => 'Estamos fazendo uma manutenção rápida. Volte a conferir em breve.',
    ],

    504 => [
        'title' => 'Isso demorou demais',
        'description' => 'O servidor não respondeu a tempo. Tente novamente.',
    ],

    'default' => [
        'title' => 'Algo não saiu como esperado',
        'description' => 'Não conseguimos concluir sua solicitação. Volte e tente novamente.',
    ],
];
