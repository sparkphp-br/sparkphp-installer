# SparkPHP Installer

Instalador global do [SparkPHP](https://github.com/sparkphp-br/sparkphp) — cria novos projetos com um único comando.

## Requisitos

- PHP >= 8.3
- Composer

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

## Comandos

### `sparkphp new <nome>`

Cria um novo projeto SparkPHP.

```bash
sparkphp new meu-projeto
```

Em modo interativo, o instalador pergunta:

```
Incluir documentação no projeto? [yes]:
Inicializar repositório Git? [yes]:
```

**Opções disponíveis:**

| Opção | Descrição |
|---|---|
| `--no-docs` | Cria o projeto sem a pasta `docs/` |
| `--git` | Inicializa repositório Git automaticamente |
| `--no-git` | Não inicializa repositório Git |

**Exemplos:**

```bash
# Com todas as opções explícitas (sem perguntas)
sparkphp new meu-projeto --no-docs --git

# Sem documentação e sem Git
sparkphp new meu-projeto --no-docs --no-git

# Não interativo (CI/scripts) — usa os padrões
sparkphp new meu-projeto -n
```

---

### `sparkphp self-update`

Atualiza o installer para a versão mais recente.

```bash
sparkphp self-update
```

Aliases: `selfupdate`

---

### `sparkphp --version`

Exibe a versão atual do installer.

```bash
sparkphp --version
```

## Licença

MIT
