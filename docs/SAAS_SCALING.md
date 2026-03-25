# SaaS Scaling Readiness

## Multi Team
- Introduce `teams` and `team_members` tables.
- Support role matrix per tenant (owner, manager, staff).
- Move `tenant_id` to `team_id` where applicable.

## Mobile API
- Expose `/api/v1` endpoints with token auth.
- Keep tenant isolation via scope + policy.
- Add API resources/transformers for stable contracts.

## Webhooks
- Outbound webhook subscriptions table.
- Queue-based delivery workers.
- Signature validation and replay protection.
