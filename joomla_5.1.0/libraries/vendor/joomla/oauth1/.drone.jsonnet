local volumes = [
    {
        name: "composer-cache",
        path: "/tmp/composer-cache",
    },
];

local hostvolumes = [
    {
        name: "composer-cache",
        host: {path: "/tmp/composer-cache"}
    },
];

local composer(phpversion, params) = {
    name: "composer",
    image: "joomlaprojects/docker-images:php" + phpversion,
    volumes: volumes,
    commands: [
        "php -v",
        "composer update " + params,
    ]
};

local phpunit(phpversion) = {
    name: "PHPUnit",
    image: "joomlaprojects/docker-images:php" + phpversion,
    [if phpversion == "8.3" then "failure"]: "ignore",
    commands: ["vendor/bin/phpunit"]
};

local pipeline(name, phpversion, params) = {
    kind: "pipeline",
    name: "PHP " + name,
    volumes: hostvolumes,
    steps: [
        composer(phpversion, params),
        phpunit(phpversion)
    ],
};

[
    {
        kind: "pipeline",
        name: "Codequality",
        volumes: hostvolumes,
        steps: [
            {
                name: "composer",
                image: "joomlaprojects/docker-images:php8.1",
                volumes: volumes,
                commands: [
                    "php -v",
                    "composer update"
                ]
            },
            {
                name: "phpcs",
                image: "joomlaprojects/docker-images:php8.1",
                depends: [ "composer" ],
                commands: [
                    "vendor/bin/phpcs --standard=ruleset.xml src/"
                ]
            },
            {
                name: "phan",
                image: "joomlaprojects/docker-images:php8.1-ast",
                depends: [ "composer" ],
                failure: "ignore",
                commands: [
                    "vendor/bin/phan"
                ]
            },
            {
                name: "phpstan",
                image: "joomlaprojects/docker-images:php8.1",
                depends: [ "composer" ],
                failure: "ignore",
                commands: [
                    "vendor/bin/phpstan analyse src",
                ]
            },
            {
                name: "phploc",
                image: "joomlaprojects/docker-images:php8.1",
                depends: [ "composer" ],
                failure: "ignore",
                commands: [
                    "phploc src",
                ]
            },
            {
                name: "phpcpd",
                image: "joomlaprojects/docker-images:php8.1",
                depends: [ "composer" ],
                failure: "ignore",
                commands: [
                    "phpcpd src",
                ]
            }
        ]
    },
    pipeline("8.1 lowest", "8.1", "--prefer-stable --prefer-lowest"),
    pipeline("8.1", "8.1", "--prefer-stable"),
    pipeline("8.2", "8.2", "--prefer-stable"),
]
