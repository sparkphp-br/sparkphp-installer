# SparkPHP Installer

Instalador global do [SparkPHP](https://github.com/sparkphp-br/sparkphp) — cria novos projetos com um único comando.

## Instalação

```bash
composer global require sparkphp-br/installer
```

Certifique-se de que o diretório de binários globais do Composer está no seu `PATH`:

```bash
# Linux / macOS
export PATH="$HOME/.config/composer/vendor/bin:$PATH"

# ou, dependendo do ambiente:
export PATH="$HOME/.composer/vendor/bin:$PATH"
```

Para descobrir o caminho correto no seu ambiente:

```bash
composer global config home
```

## Uso

```bash
sparkphp new meu-projeto
```

Isso cria o diretório `meu-projeto/` com a estrutura completa do SparkPHP e instala as dependências automaticamente via Composer.

Ao rodar o comando, será perguntado se deseja incluir a documentação no projeto:

```
Incluir documentação no projeto? [yes]:
```

Para pular a pergunta, use a flag `--no-docs`:

```bash
sparkphp new meu-projeto --no-docs
```

## Requisitos

- PHP >= 8.3
- Composer

## Licença

MIT
