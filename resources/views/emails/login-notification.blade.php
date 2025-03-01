<x-mail::message>
# Detekováno nové přihlášení k vašemu účtu

Vážený uživateli {{ $user->name }},

Detekovali jsme nové přihlášení k vašemu účtu v aplikaci {{ config('app.name') }}.

**Detaily přihlášení:**
- **Datum a čas:** {{ date('d.m.Y H:i:s', strtotime($loginRecord->created_at)) }}
- **IP adresa:** {{ $loginRecord->ip_address }}
- **Zařízení:** {{ $loginRecord->user_agent }}
@if($loginRecord->location)
- **Lokace:** {{ $loginRecord->location }}
@endif

@if($isSuspicious)
**⚠️ Toto přihlášení bylo označeno jako potenciálně podezřelé!**

Pokud jste se nepřihlásili vy, doporučujeme okamžitě změnit své heslo a kontaktovat podporu.
@else
Pokud jste se přihlásili vy, můžete tuto zprávu ignorovat.
@endif

<x-mail::button :url="$url">
Zkontrolovat historii přihlášení
</x-mail::button>

Pokud jste se nepřihlásili vy, změňte si okamžitě heslo.

S pozdravem,<br>
Tým {{ config('app.name') }}

<p style="font-size: 12px; color: #666;">
Tyto notifikace můžete vypnout v nastavení svého účtu v sekci Zabezpečení.
</p>
</x-mail::message>
