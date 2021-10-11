<?php 
	return $config = [
		'instances' => [
			'back' => [
				'path' => '{your_path}',
				'comands' => [
					'git checkout --all',
					'{action}',
					'composer install',
					'php artisan migrate',
					'php artisan queue:restart'
				]
			],
			'front' => [
				'path' => '{your_path}',
				'comands' => [
					'cp .env.production .env.production.test',
					'git checkout -- .env.production',
					'{action}',
					'cp -f .env.production.test  .env.production',
					'npm install',
					'npm run build',
					'pm2 restart  all'
				]
			]
		]
	];
?>

