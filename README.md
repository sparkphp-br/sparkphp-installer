# SparkPHP Installer

Instalador global do [SparkPHP](https://github.com/sparkphp-br/sparkphp) — cria novos projetos com um único comando.

## Requisitos

- PHP >= 8.3
- Composer

## Instalação

```bash
composer global require sparkphp-br/installer
```

Após a instalação, o installer verifica automaticamente se o diretório de binários do Composer está no seu `PATH` e oferece configurá-lo caso necessário.

Se preferir configurar manualmente, adicione ao seu `~/.bashrc` ou `~/.zshrc`:

```bash
export PATH="$HOME/.config/composer/vendor/bin:$PATH"
```

## Uso

Ao digitar apenas `sparkphp`, um menu interativo é exibido com a versão atual, notificação de atualização disponível e as opções de ação:

```
  ⚡ SparkPHP Installer  v1.1.4

  ✓ Você está na versão mais recente

 O que deseja fazer? [Criar novo projeto]:
  [0] Criar novo projeto
  [1] Sair
```

## Comandos

### `sparkphp new <nome>`

Cria um novo projeto SparkPHP.

```bash
sparkphp new meu-projeto
```

O nome do projeto é normalizado automaticamente: espaços viram hífens e acentos são removidos.

```bash
sparkphp new "crm acessórios"
# Nome ajustado para: crm-acessorios
```

Em modo interativo, o instalador pergunta:

```
Nome do projeto:
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

### `sparkphp path:setup`

Verifica se o diretório de binários do Composer está no `PATH` e o configura automaticamente.

```bash
sparkphp path:setup
```

Detecta o shell em uso (`bash`, `zsh`, `fish`) e escreve a configuração no arquivo correto. Use `-y` para confirmar sem perguntas:

```bash
sparkphp path:setup -y
```

---

### `sparkphp --version`

Exibe a versão atual do installer.

```bash
sparkphp --version
```

## Licença

MIT
