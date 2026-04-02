# Diretrizes para o Claude — sparkphp/installer

## A cada nova feature ou correção, sempre fazer:

### 1. Atualizar `src/Application.php`
- Incrementar `VERSION` seguindo semver:
  - `patch` (X.Y.Z+1) — correções de bug
  - `minor` (X.Y+1.0) — nova funcionalidade sem quebrar compatibilidade
  - `major` (X+1.0.0) — mudança que quebra compatibilidade

### 2. Atualizar `README.md`
- Documentar qualquer novo comando ou opção
- Atualizar exemplos se o comportamento mudou
- Atualizar a versão exibida no exemplo do menu interativo

### 3. Commit, tag e push — sempre no mesmo número

```bash
git add src/Application.php README.md
git commit -m "chore: bump version to X.Y.Z"
git tag vX.Y.Z
git push origin master
git push origin vX.Y.Z
```

> NUNCA criar a tag antes de atualizar o VERSION. A tag deve sempre apontar para o commit que já contém o VERSION correto.

### 4. Mensagens de commit

Seguir Conventional Commits:

| Prefixo | Quando usar |
|---|---|
| `feat:` | Nova funcionalidade |
| `fix:` | Correção de bug |
| `docs:` | Apenas documentação |
| `chore:` | Versão, dependências, configuração |
| `refactor:` | Refatoração sem mudar comportamento |

## Estrutura do projeto

```
sparkphp-installer/
├── bin/sparkphp              # Executável CLI
├── src/
│   ├── Application.php       # VERSION centralizada aqui
│   └── Commands/
│       ├── WelcomeCommand.php    # Menu interativo (comando padrão)
│       ├── NewCommand.php        # sparkphp new <nome>
│       ├── SelfUpdateCommand.php # sparkphp self-update
│       └── PathSetupCommand.php  # sparkphp path:setup
├── composer.json
└── README.md
```

## Checklist antes de fazer push

- [ ] `Application::VERSION` atualizado
- [ ] `README.md` reflete as mudanças
- [ ] Commit feito com mensagem no padrão
- [ ] Tag criada com o mesmo número do VERSION
- [ ] Push do branch e da tag
