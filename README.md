# 🚀 ShipSmart Test Dev — Fullstack Challenge

Aplicação Fullstack construída como parte do teste técnico para a vaga de **Tech Lead Fullstack**, utilizando **Laravel 12 (PHP 8.3)** no backend e **Vue 3 + Vite** no frontend.
O objetivo foi entregar uma solução funcional, bem estruturada e coerente com o enunciado, priorizando clareza, qualidade e boas práticas reais de desenvolvimento.

---

## 📑 Sumário

- [🛠️ Contexto e Decisões de Arquitetura](#contexto)
- [⚙️ Tecnologias Utilizadas](#tecnologias)
- [🏗️ Estrutura e Organização do Projeto](#estrutura)
- [🚀 Como Rodar o Projeto](#como-rodar)
- [✅ Testes e Documentação](#testes)
- [⚡️ Decisões de Simplicidade e Design](#decisoes)
- [⚙️ Configuração de Ambiente (`.env`)](#env)
- [🙌 Considerações Finais](#consideracoes)

---

## 🛠️ Contexto e Decisões de Arquitetura <a id="contexto"></a>

O desafio consistia em criar um CRUD de contatos com integração de CEP, paginação e envio de e-mails, além de configurar tudo com Docker.
✅ **Todos os requisitos do enunciado foram integralmente atendidos**, com implementação completa de backend, frontend, filas, cache e envio de e-mails.

Mantive a solução enxuta e direta, respeitando o escopo do teste e demonstrando domínio técnico, organização e boas práticas de arquitetura. O foco foi construir algo realista e escalável, mas sem overengineering.

**Principais decisões e justificativas:**

- Estruturei o backend em **camadas de Repository e Service**, seguindo o enunciado e mantendo o código desacoplado. Isso facilita testes, manutenção e futuras evoluções.
- Criei **validação de CEP** no backend e frontend, com **fallback em cache** na Rule, garantindo desempenho e tolerância a falhas na API externa.
- No backend, a integração com o **ViaCEP** utiliza cache local e validação automática via Rule. No frontend, o comportamento foi replicado para experiência instantânea.
- Em produção, a URL do ViaCEP e outras integrações ficariam configuradas em `.env` ou no `config/services.php`, garantindo flexibilidade de ambiente.
- Substituí o sistema de `Mailable` padrão do Laravel pelo **sistema de Notifications**, pois é mais escalável e facilmente extensível para múltiplos canais (e-mail, SMS, Slack, etc.).
- Usei **PrimeVue** para acelerar a criação das interfaces e focar nas regras de negócio. Em um projeto real, eu criaria componentes próprios com **TailwindCSS**.
- Configurei **i18n** para internacionalização e facilidade de expansão futura, mesmo sendo um teste simples.
- Criei **meus próprios Dockerfiles** ao invés de usar o Laravel Sail. Isso tornou a imagem mais leve, compatível com produção e alinhada ao padrão que costumo usar em projetos reais.
- A estrutura de containers está centralizada na raiz com `docker-compose.yml`, garantindo praticidade para levantar todo o ambiente de uma vez.
- O projeto já vem configurado com **Redis** e **MailHog** via Docker, facilitando os testes de cache, filas e envio de e-mails sem necessidade de ajustes adicionais. A explicação detalhada do `.env` está descrita na seção [Configuração de Ambiente](#env).

---

## ⚙️ Tecnologias Utilizadas <a id="tecnologias"></a>

### Backend

- PHP 8.3 / Laravel 12
- Redis (filas e cache)
- MailHog (SMTP fake)
- Pest (testes)
- SQLite (banco leve e portátil)

### Frontend

- Vue 3 (Composition API)
- PrimeVue (componentes prontos)
- Vite (build rápido e leve)
- Axios (requisições HTTP)
- Pinia (estado global)
- i18n (internacionalização)

### Infraestrutura

- Docker Compose (orquestração completa)
- Containers: backend, frontend, Redis, MailHog

---

## 🏗️ Estrutura e Organização do Projeto <a id="estrutura"></a>

```
shipsmart-test-dev/
│
├── backend/
│   ├── app/
│   │   ├── Exports/
│   │   ├── Http/
│   │   ├── Models/
│   │   ├── Notifications/
│   │   ├── Providers/
│   │   ├── Repositories/
│   │   ├── Rules/
│   │   ├── Services/
│   │   └── Support/
│   ├── routes/
│   ├── tests/
│   ├── .env.example
│   ├── composer.json
│   ├── phpunit.xml
│   └── README.md
│
├── frontend/
│   ├── src/
│   │   ├── api/
│   │   ├── assets/
│   │   ├── components/
│   │   ├── composables/
│   │   ├── layouts/
│   │   ├── modules/
│   │   │   └── contacts/
│   │   │       ├── data/estados.ts
│   │   │       ├── views/
│   │   │       │   ├── Form.vue
│   │   │       │   └── List.vue
│   │   │       ├── api.ts
│   │   │       ├── store.ts
│   │   │       └── types.ts
│   │   ├── router/
│   │   ├── App.vue
│   │   ├── main.ts
│   │   └── style.css
│   ├── .env.example
│   ├── vite.config.ts
│   └── README.md
│
├── docker-compose.yml
├── Dockerfile.backend
├── Dockerfile.frontend
└── README.md
```

**Camadas principais:**

- **Repositories:** acesso direto ao Eloquent e queries personalizadas.
- **Services:** regra de negócio e orquestração entre camadas.
- **Rules:** validações específicas como a de CEP.
- **Notifications:** camada de envio de e-mails e notificações, com suporte a outros canais.
- **Jobs:** processamento assíncrono para filas e eventos.

---

## 🚀 Como Rodar o Projeto <a id="como-rodar"></a>

### 1️⃣ Clone o repositório

> ⚠️ **Pré-requisitos:** Docker e Docker Compose instalados.
>
> 💡 Todo o ambiente já está containerizado, não é necessário ter PHP, Composer ou Node instalados localmente.

```bash
# HTTPS
git clone https://github.com/vitorpedroso283/shipsmart-test-dev.git

# ou via SSH
git clone git@github.com:vitorpedroso283/shipsmart-test-dev.git

cd shipsmart-test-dev
```

### 2️⃣ Configure os arquivos `.env`

Antes de iniciar, é necessário copiar os arquivos de exemplo de ambiente para ativar as variáveis locais:

```bash
# Backend
cp backend/.env.example backend/.env

# Crie o banco SQLite se ainda não existir
mkdir -p backend/database
touch backend/database/database.sqlite

# Frontend
cp frontend/.env.example frontend/.env
```

### 3️⃣ Suba os containers, gere a chave e execute as migrações

```bash
docker compose up -d --build
docker exec -it shipsmart_backend php artisan key:generate
docker exec -it shipsmart_backend php artisan migrate
```

### 4️⃣ Acesse as aplicações

- Frontend: [http://localhost:5173](http://localhost:5173)
- Backend: [http://localhost:8000/api](http://localhost:8000/api)
- MailHog: [http://localhost:8025](http://localhost:8025)

A documentação Swagger está disponível em:

```
http://localhost:8000/api/documentation
```

Caso queira alterar configurações locais, basta copiar o `.env.example` e criar seu `.env` personalizado em cada ambiente.

---

## ✅ Testes e Documentação <a id="testes"></a>

Os testes foram escritos com **Pest** por oferecer uma sintaxe mais simples e legível.

Executar os testes:

```bash
docker exec -it shipsmart_backend php artisan test
```

A documentação foi gerada com **Swagger (OpenAPI)** e está disponível na rota `http://localhost:8000/api/documentation`.
Em projetos reais, costumo usar **Postman Collections** para facilitar a integração e versionamento.

---

### ⚡️ Limite de Requisições (Rate Limiting)

Implementei **rate limiting** na API para garantir estabilidade e evitar abuso de requisições. Todas as rotas estão protegidas pelo middleware `throttle:100,1`, que limita **100 requisições por minuto por IP**.

Isso é suficiente para ambientes de teste e demonstração, evitando sobrecarga e simulando um comportamento mais próximo de um ambiente real de produção.

---

## ⚙️ Configuração de Ambiente (`.env`) <a id="env"></a>

O `docker-compose.yml` já inclui os serviços essenciais como **Redis**, **MailHog** e o **banco SQLite**, todos prontos para uso imediato.
O MailHog pode ser acessado em [http://localhost:8025](http://localhost:8025) para visualizar os e-mails enviados durante os testes de notificação.

O projeto utiliza um `.env` otimizado para Docker, com foco em cache, filas e notificações.

### 🔹 Cache, Sessões e Filas

Utiliza **Redis** para cache, filas e sessões, garantindo desempenho e consistência entre containers:

```env
CACHE_STORE=redis
SESSION_DRIVER=database
QUEUE_CONNECTION=redis
REDIS_CLIENT=phpredis
REDIS_HOST=redis
REDIS_PORT=6379
```

### 🔹 E-mail (MailHog)

O envio de e-mails é realizado via **MailHog**, facilitando testes locais sem necessidade de SMTP externo:

```env
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_FROM_ADDRESS="no-reply@shipsmart.local"
MAIL_FROM_NAME="${APP_NAME}"
```

### 🔹 Notificações

O sistema de **Notifications** está configurado para enviar mensagens para o e-mail principal definido:

```env
NOTIFICATION_MAIL=vitorsamuel283@gmail.com
```

Essas variáveis já funcionam automaticamente dentro do ambiente Docker, sem necessidade de ajustes manuais.

---

## 🙌 Considerações Finais <a id="consideracoes"></a>

Este projeto reflete meu estilo de desenvolvimento: prático, limpo e escalável.
Busquei equilíbrio entre boa arquitetura e simplicidade, evitando exageros e focando no que realmente agrega valor.

Cada decisão foi pensada para mostrar conhecimento técnico aplicado, mantendo a entrega leve, compreensível e profissional.

**Obrigado pela oportunidade! ☕️**

---
📬 **Contato**
[LinkedIn](https://linkedin.com/in/vitorpedroso) • [GitHub](https://github.com/vitorpedroso283)
