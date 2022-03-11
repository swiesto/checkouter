# checkouter

location ~* ^/api/web/travel/gitcheckouter/(.*)\.(jpeg|jpg|png|gif|txt|svg|js|woff|woff2|css|map)$ {
    set $root_path /home/omni/gitcheckouter;
    root $root_path;
    try_files /$1.$2 $uri/ =404;
  }

  location  ~* ^/api/web/travel/gitcheckouter/ {
    auth_basic "Здарова, доступы есть?";
    auth_basic_user_file /etc/nginx/htpasswd;
    set $root_path /home/omni/gitcheckouter;
    root $root_path;
    fastcgi_pass $php_sock;
    include fastcgi_params;
    fastcgi_param SCRIPT_FILENAME $document_root/index.php;
    fastcgi_param PHP_ADMIN_VALUE $sendmail_path;
  }
  
известные баги:
Поиск веток
Сделать фронт рабочим
Убрать баг с созданной remote веткой