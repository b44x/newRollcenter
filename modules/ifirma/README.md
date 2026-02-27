ifirma-prestashop-plugin
========================

Wtyczka do sklepów PrestaShop w wersji 1.7+ i 8.2 przeznaczona do integracji z serwisem ifirma.pl
Wymaga PHP 8.1+ (lub nowszej) oraz obsługi curl.

## Wersja 1.8 (kompatybilna z PS 8.2)

### Zmiany:
- Migracja hooka `adminOrder` na `displayAdminOrderMain` dla kompatybilności z PS 8.
- Przeniesienie logiki wysyłania i pobierania faktur do kontrolerów Symfony z bezpiecznym routowaniem.
- Aktualizacja szablonów do nowego stylowania admin PS 8 (card zamiast panel).
- Usunięcie bezpośredniego dostępu do plików PHP dla bezpieczeństwa.
- Kompatybilność z PHP 8.1/8.2 i Symfony 5/6.
- Zachowana pełna funkcjonalność modułu.