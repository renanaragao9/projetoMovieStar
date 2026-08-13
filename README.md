# MovieStar

Plataforma web de críticas de filmes. Usuários cadastram filmes, avaliam e comentam as obras de outros usuários. Desenvolvido em PHP puro com MySQL, sem frameworks.

## Funcionalidades

- Cadastro, login e logout com autenticação por token de sessão
- Senhas armazenadas com hash (`password_hash`)
- CRUD completo de filmes (cadastro, edição, exclusão e listagem)
- Upload de imagens de capa (PNG/JPG convertidas para JPG)
- Sistema de críticas com nota (1 a 10) e comentário — uma avaliação por usuário por filme
- Nota média do filme calculada a partir das avaliações
- Busca de filmes por título
- Dashboard com os filmes cadastrados pelo usuário
- Edição de perfil com foto, bio e alteração de senha
- Mensagens de feedback (flash messages) de sucesso e erro

## Tecnologias

- PHP (POO, PDO, `password_hash`, `random_bytes`)
- MySQL
- Bootstrap 4 (CDN)
- Font Awesome (CDN)

## Estrutura do projeto

```
├── globals.php           # Inicia sessão e define $BASE_URL (raiz do projeto)
├── index.php             # Redireciona para a home (pages/movies/index.php)
├── css/                  # Estilos do projeto
├── dao/                  # Data Access Objects (MovieDAO, UserDAO, ReviewDAO)
├── database/             # Conexão PDO (db.php), migrations e runner (php database/migrate.php)
├── img/                  # Logo, capas e fotos de perfil
├── models/               # Entidades (Movie, User, Review, Message)
├── pages/                # Páginas e rotas organizadas por feature
│   ├── auth/             # Login/cadastro, logout e seus processos
│   ├── movies/           # Home, filme, busca, dashboard, novo/editar e processos
│   └── users/            # Perfil público, edição de perfil e processos
└── templates/            # Header, footer, cards e componentes reutilizáveis
```

## Requisitos

- PHP 7.4+ (com extensão PDO MySQL e GD para upload de imagens)
- MySQL 5.7+ / MariaDB
- Servidor web (Apache com `mod_rewrite` ou `php -S`)

## Instalação

1. Clone o repositório na raiz do seu servidor web:

```bash
git clone https://github.com/renanaragao9/projetoMovieStar.git
```

2. Crie o arquivo `.env` a partir do exemplo e ajuste os valores:

```bash
cp .env.example .env
```

```env
DB_NAME=moviestar
DB_HOST=localhost
DB_PORT=3306
DB_USER=root
DB_PASS=
```

O `.env` é carregado automaticamente por `database/db.php`. Variáveis de ambiente reais têm prioridade sobre o arquivo — útil para ambientes Docker/CI sem alterar código.

3. Rode as migrations — o banco e as tabelas são criados automaticamente:

```bash
php database/migrate.php up
```

Outros comandos disponíveis:

```bash
php database/migrate.php status      # Lista migrations aplicadas e pendentes
php database/migrate.php down        # Reverte a última migration
php database/migrate.php down --all  # Reverte todas as migrations
php database/migrate.php fresh       # Reverte tudo e aplica do zero
```

4. Garanta permissão de escrita na pasta `img/movies` (upload das capas).

5. Acesse o projeto pelo navegador, ex.: `http://localhost/projetoMovieStar`.

## Uso

1. Crie uma conta na página **Entrar / Cadastrar**
2. Cadastre filmes em **Incluir Filme** (título, descrição, trailer, categoria, duração e capa)
3. Na home, clique em um filme para ver detalhes e avaliar
4. Envie nota (1 a 10) e comentário — cada usuário avalia cada filme uma única vez
5. Gerencie seus filmes no **Dashboard** (editar/excluir)

## Observações

- A autenticação usa token aleatório (`bin2hex(random_bytes(50))`) salvo na sessão e no banco
- O arquivo `review_process.php` e o método `ReviewDAO::create` contêm `print_r` de depuração que pode ser removido em produção
- `globals.php` monta a `$BASE_URL` dinamicamente — em subpasta ou na raiz do servidor, sem ajustes manuais
