<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Exception;

class RefreshCardsVariantsTypesMv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cards:refresh-variants-types-mv {--force : Přepíše data bez potvrzení} {--stats : Zobrazí statistiky po refresh}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh materialized view cards_variants_types_mv pro rychlé načítání variant karet';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $force = $this->option('force');
        $showStats = $this->option('stats');

        // Kontrola existence tabulky
        $tableExists = $this->checkTableExists();
        
        if (!$tableExists) {
            $this->error('Tabulka cards_variants_types_mv neexistuje. Spusťte nejprve migrations.');
            return self::FAILURE;
        }

        if (!$force) {
            if (!$this->confirm('Chcete obnovit data v tabulce cards_variants_types_mv?')) {
                $this->info('Operace zrušena.');
                return self::SUCCESS;
            }
        }

        $this->info('Začínám refresh materialized view cards_variants_types_mv...');

        try {
            // Spuštění refresh operace
            $this->refreshMaterializedView();
            
            $this->info('✅ Materialized view úspěšně obnovena.');

            // Zobrazení statistik pokud je požadováno
            if ($showStats) {
                $this->displayStats();
            }

            return self::SUCCESS;

        } catch (Exception $e) {
            $this->error('❌ Chyba při refresh materialized view: ' . $e->getMessage());
            return self::FAILURE;
        }
    }

    /**
     * Kontrola existence tabulky
     */
    private function checkTableExists(): bool
    {
        try {
            DB::table('cards_variants_types_mv')->count();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Refresh materialized view - truncate a naplnění novými daty
     */
    private function refreshMaterializedView(): void
    {
        $this->line('🔄 Obnovuji materialized view...');

        // Krok 1: Vyprázdnění tabulky
        $this->line('   Vyprazdňuji existující data...');
        DB::table('cards_variants_types_mv')->truncate();

        // Krok 2: Naplnění novými daty
        $this->line('   Načítám nová data...');
        DB::statement("
            INSERT INTO cards_variants_types_mv (
                card_id, cm_id, variant_type_code, variant_type_name, 
                variant_type_description, price_column_suffix, variant,
                cm_expansion_id, cm_metacard_id, collector_number, 
                ptcgo_code, tcgplayer_id, rarity, date_added,
                variant_normal, variant_holo, variant_reverse, variant_promo,
                variant_pokeball, variant_masterball, is_primary_variant,
                last_updated
            )
            SELECT 
                cv.card_id,
                cv.cm_id,
                cvt.code as variant_type_code,
                cvt.name as variant_type_name,
                cvt.description as variant_type_description,
                cvt.price_column_suffix,
                cv.variant,
                cv.cm_expansion_id,
                cv.cm_metacard_id,
                cv.collector_number,
                cv.ptcgo_code,
                cv.tcgplayer_id,
                cv.rarity,
                cv.date_added,
                cv.variant_normal,
                cv.variant_holo,
                cv.variant_reverse,
                cv.variant_promo,
                cv.variant_pokeball,
                cv.variant_masterball,
                CASE 
                    WHEN ROW_NUMBER() OVER (PARTITION BY cv.card_id, cvt.code ORDER BY cv.cm_id) = 1 
                    THEN 1 
                    ELSE 0 
                END as is_primary_variant,
                NOW() as last_updated
            FROM cards_variant cv
            INNER JOIN cards_variant_types cvt ON cv.variant = cvt.variant
            WHERE cv.card_id IS NOT NULL
            ORDER BY cv.card_id, cvt.code, cv.cm_id
        ");

        $this->line('✅ Data úspěšně obnovena.');
    }

    /**
     * Zobrazení statistik materialized view
     */
    private function displayStats(): void
    {
        try {
            $this->line('');
            $this->line('📊 Statistiky materialized view:');
            $this->line('─────────────────────────────────');

            // Celkový počet záznamů
            $totalRecords = DB::table('cards_variants_types_mv')->count();
            $this->line("   Celkem záznamů: {$totalRecords}");

            // Počet unikátních karet
            $uniqueCards = DB::table('cards_variants_types_mv')->distinct('card_id')->count('card_id');
            $this->line("   Unikátních karet: {$uniqueCards}");

            // Počet unikátních variant
            $uniqueVariants = DB::table('cards_variants_types_mv')->distinct('cm_id')->count('cm_id');
            $this->line("   Unikátních variant: {$uniqueVariants}");

            // Počet typů variant
            $variantTypes = DB::table('cards_variants_types_mv')->distinct('variant_type_code')->count('variant_type_code');
            $this->line("   Typů variant: {$variantTypes}");

            // Poměr variant na kartu
            $avgVariantsPerCard = $totalRecords > 0 && $uniqueCards > 0 ? round($totalRecords / $uniqueCards, 2) : 0;
            $this->line("   Průměr variant na kartu: {$avgVariantsPerCard}");

            // Čas posledního update
            $lastUpdated = DB::table('cards_variants_types_mv')->max('last_updated');
            $this->line("   Posledně aktualizováno: {$lastUpdated}");

            $this->line('');

        } catch (Exception $e) {
            $this->error('Chyba při získávání statistik: ' . $e->getMessage());
        }
    }
}
