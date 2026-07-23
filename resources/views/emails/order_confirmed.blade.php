<x-mail::message>
# Olá! A sua encomenda foi registada.

Obrigado por comprar na **Mercearia Orestes**. Aqui estão os detalhes para o levantamento:

<x-mail::panel>
**Código de Levantamento:** {{ $encomenda->order_code }}
</x-mail::panel>

**Total a pagar:** {{ number_format($encomenda->order_cost, 2) }}€

Apresente este código na loja quando vier levantar os seus produtos.

Até breve,<br>
{{ config('app.name') }}
</x-mail::message>