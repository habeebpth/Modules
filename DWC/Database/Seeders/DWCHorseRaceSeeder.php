<?php

namespace Modules\DWC\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\DWC\Entities\DwcHorse;
use Modules\DWC\Entities\DwcRaces;

class DWCHorseRaceSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'DUBAI WORLD CUP' => [
                'LAUREL RIVER (USA)',
                'FOREVER YOUNG (JPN)',
                'USHBA TESORO (JPN)',
                'WALK OF STARS (UK)',
                'WILSON TESORO (JPN)',
                'RAMJET (JPN)',
                'DURA EREDE (JPN)',
                'HIT SHOW (USA)',
                'IMPERIAL EMPEROR (IRE)',
                'IL MIRACOLO (USA)',
                'MIXTO (USA)',
                'KATONAH (USA)',
            ],
            'LONGINES DUBAI SHEEMA CLASSIC' => [
                'CALANDAGAN (IRE)',
                'DUREZZA (JPN)',
                'REBELS ROMANCE (IRE)',
                'SHIN EMPEROR (FR)',
                'GIAVELLOTTO (IRE)',
                'AL RIFFA (FR)',
                'DANON DECILE (JPN)',
                'CALIF (GER)',
                'SILVER KNOTT (GB)',
                'CERVINIA (JPN)',
                'DEIRA MILE (IRE)',
            ],
            'DUBAI TURF' => [
                'ROMANTIC WARRIOR (IRE)',
                'SOUL RUSH (JPN)',
                'FACTEUR CHEVAL (IRE)',
                'GHOSTWRITER (IRE)',
                'MALJOOM (IRE)',
                'LIBERTY ISLAND (JPN)',
                'BREDE WEG (JPN)',
                'NATIONS PRIDE (IRE)',
                'MEISHO TABARU (JPN)',
                'GOEMON  (BH)',
                'HOLLOWAY BOY (GB)',
                'POINT LYNAS  (IRE)',
            ],
            'DUBAI GOLDEN SHAHEEN' => [
                'TUZ (USA)',
                'STRAIGHT NO CHASER (USA)',
                'REMAKE (JPN)',
                'NAKATOMI (USA)',
                'DON FRANKIE (JPN)',
                'JASPER KRONE (USA)',
                'SUPER CHOW  (USA)',
                'KUROJISHI JOE (JPN)',
                'AMERICAN STAGE (USA)',
                'COLOUR UP (IRE)',
                'EASTERN WORLD (IRE)',
                'DREW\'S GOLD (USA)',
            ],
            'AL QUOZ SPRINT' => [
                'AUDIENCE (UK)',
                'MONTASSIB (GB)',
                'REGIONAL  (GB)',
                'WEST ACRE (IRE)',
                'BELIEVING (IRE)',
                'DANON MCKINLEY (JPN)',
                'ISIVUNGUVUNGU (SAF)',
                'STAR OF MYSTERY (UK)',
                'HOWDEEPISYOURLOVE (AUS)',
                'MARBAAN (GB)',
                'MITBAAHY (IRE)',
                'WIN CARNELIAN (JPN)',
                'COPPOLA  (USA)',
                'PURO MAGIC (JPN)',
                'ROMANTIC STYLE (IRE)',
            ],
            'DUBAI GOLD CUP' => [
                'TRAWLERMAN (IRE)',
                'DOUBLE MAJOR (IRE)',
                'STRAIGHT  (GER)',
                'SEVENNA\'S KNIGHT (IRE)',
                'AL NAYYIR (GB)',
                'DUBAI FUTURE (GB)',
                'CONTINUOUS  (JPN)',
                'TRAFALGAR SQUARE (FR)',
                'KEFFAAF (GB)',
                'KING OF CONQUEST (GB)',
                'PASSION AND GLORY (IRE)',
                'TERM OF ENDEARMENT (GB)',
                'EPIC POET (IRE)',
            ],
            'GODOLPHIN MILE' => [
                'PEPTIDE NILE (JPN)',
                'BOOK\'EM DANNO (USA)',
                'RAGING TORRENT (USA)',
                'MUFASA (CHI)',
                'KAZU PETRIN (JPN)',
                'KING GOLD (FR)',
                'SWORD POINT (AUS)',
                'MESHTRI (USA)',
                'LITTLE VIC (USA)',
                'NO LUNCH (IRE)',
                'STEAL SUNSHINE (USA)',
                'QAREEB (USA)',
                'FORT PAYNE (FR)',
            ],
            'UAE DERBY' => [
                'FLOOD ZONE  (USA)',
                'SHIN FOREVER (USA)',
                'DRAGON (JPN)',
                'ADMIRE DAYTONA (JPN)',
                'DON IN THE MOOD (JPN)',
                'GALACTIC STAR (USA)',
                'HEART OF HONOR (UK)',
                'ROYAL FAVOUR (GB)',
                'HONEST MOON (USA)',
                'UNDEFEATED (USA)',
                'QUEEN AZTECA (USA)',
            ],
            'DUBAI KAHAYLA CLASSIC' => [
                'TILAL AL KHALEDIAH (KSA)',
                'UNLEASHED (USA)',
                'BARAKKA (UK)',
                'ASFAN AL KHALEDIAH (KSA)',
                'FIRST CLASSS (USA)',
                'KANAILLE DE FAUST (FR)',
                'AF MAQAM (AE)',
                'BAHWAN (FR)',
                'MUBEED (AE)',
                'SUNY DU LOUP  (FR)',
                'DJAFAR (FR)',
                'VICA GRINE (FR)',
                'VIZHIR (FR)',
                'RB MARY LYLAH (USA)',
                'TARIQ (FR)',
            ],
        ];

        foreach ($data as $raceName => $horses) {
            // Insert Race
            $race = DwcRaces::firstOrCreate(['name' => $raceName]);

            foreach ($horses as $horseName) {
                // Insert Horse
                $horse = DwcHorse::firstOrCreate(['name' => $horseName]);

                // Attach to Pivot Table
                $horse->races()->syncWithoutDetaching([$race->id]);
            }
        }
    }
}
