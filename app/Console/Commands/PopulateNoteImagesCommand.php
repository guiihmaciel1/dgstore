<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Fragrance\Models\FragranceNote;
use Illuminate\Console\Command;

class PopulateNoteImagesCommand extends Command
{
    protected $signature = 'fragrance:populate-note-images
                            {--dry-run : Mostra o que seria atualizado sem alterar o banco}';

    protected $description = 'Preenche image_url das notas olfativas usando o catálogo Fragrantica';

    private const BASE_URL = 'https://fimgs.net/mdimg/sastojci/t.';

    public function handle(): int
    {
        $map = $this->getNoteImageMap();
        $isDryRun = $this->option('dry-run');

        $notes = FragranceNote::whereNull('image_url')
            ->orWhere('image_url', '')
            ->get();

        $updated = 0;
        $skipped = 0;

        foreach ($notes as $note) {
            $key = mb_strtolower(trim($note->name));

            if (! isset($map[$key])) {
                $skipped++;
                if ($isDryRun) {
                    $this->line("  <fg=yellow>⚠</> Sem mapeamento: {$note->name}");
                }
                continue;
            }

            $imageUrl = self::BASE_URL . $map[$key] . '.jpg';

            if ($isDryRun) {
                $this->line("  <fg=green>✓</> {$note->name} → {$imageUrl}");
            } else {
                $note->update(['image_url' => $imageUrl]);
            }

            $updated++;
        }

        $label = $isDryRun ? 'seriam atualizadas' : 'atualizadas';
        $this->newLine();
        $this->info("{$updated} notas {$label}, {$skipped} sem mapeamento.");

        return self::SUCCESS;
    }

    /**
     * Mapa: nome da nota (lowercase) => ID da imagem no Fragrantica.
     */
    private function getNoteImageMap(): array
    {
        return [
            'abacaxi' => '316',
            'absinto' => '185',
            'açafrão' => '218',
            'acorde gourmand' => '850',
            'açúcar' => '266',
            'agarwood (oud)' => '56',
            'alcaçuz' => '236',
            'alcarávia' => '304',
            'aldeídos' => '186',
            'alecrim' => '122',
            'almíscar' => '2',
            'almíscar branco' => '2',
            'âmbar' => '3',
            'âmbar cinzento' => '200',
            'âmbar claro' => '54',
            'ambreta' => '221',
            'ambrofix' => '1164',
            'ambroxan' => '333',
            'ameixa' => '108',
            'ameixa mirabelle' => '491',
            'amêndoa' => '205',
            'amêndoa amarga' => '402',
            'amêndoa cristalizada' => '205',
            'amora' => '177',
            'anis' => '100',
            'anis estrelado' => '100',
            'babosa' => '1191',
            'bagas de zimbro' => '292',
            'baunilha' => '7',
            'baunilha de bourbon' => '7',
            'baunilha de madagascar' => '7',
            'benjoim' => '57',
            'bergamota' => '16',
            'bergamota da calábria' => '16',
            'bétula' => '180',
            'cacau' => '135',
            'café' => '157',
            'camurça' => '298',
            'canela' => '31',
            'canela do ceilão' => '31',
            'caramelo' => '214',
            'cardamomo' => '104',
            'cardamomo da guatemala' => '104',
            'casca de baunilha negra' => '7',
            'cashmeran' => '237',
            'castanha' => '543',
            'cedro' => '47',
            'cedro atlas' => '47',
            'cedro da virgínia' => '47',
            'cenoura' => '340',
            'cereja' => '299',
            'chá preto chinês' => '307',
            'cidra' => '153',
            'cipreste' => '97',
            'cítricos' => '82',
            'coco' => '175',
            'coentro' => '103',
            'cominho' => '184',
            'cravo-da-índia' => '32',
            'cumarina' => '131',
            'damasco' => '907',
            'elemi' => '197',
            'erva-doce' => '100',
            'especiarias' => '1036',
            'evernil' => '130',
            'fava tonka' => '51',
            'figo' => '114',
            'flor de laranjeira' => '27',
            'flor de laranjeira tunisiana' => '27',
            'flores brancas' => '308',
            'folha de tabaco' => '146',
            'frésia' => '116',
            'frutas' => '314',
            'frutas tropicais' => '314',
            'gardênia' => '13',
            'gengibre' => '96',
            'gengibre da nigéria' => '96',
            'gerânio' => '33',
            'groselha preta' => '58',
            'groselha vermelha' => '297',
            'heliotrópio' => '50',
            'hortelã' => '193',
            'incenso' => '55',
            'íris' => '24',
            'jacarandá' => '77',
            'jasmim' => '11',
            'jasmim marroquino' => '11',
            'jasmim sambac' => '274',
            'jasmim-manga' => '151',
            'ládano' => '53',
            'laranja' => '111',
            'laranja brasileira' => '111',
            'laranja siciliana' => '111',
            'lavanda' => '34',
            'lavanda silvestre' => '34',
            'leite' => '329',
            'lichia' => '119',
            'limão' => '82',
            'limão siciliano' => '82',
            'lírio' => '14',
            'lírio-do-vale' => '14',
            'maçã' => '110',
            'maçã verde' => '112',
            'madeira de âmbar' => '195',
            'madeira de cashmere' => '445',
            'madeira guaiac' => '195',
            'madeira seca' => '1012',
            'madressilva' => '38',
            'magnólia' => '37',
            'mandarina' => '152',
            'mandarina amarela' => '152',
            'manga' => '154',
            'manjericão' => '129',
            'maracujá' => '164',
            'mel' => '211',
            'melão' => '124',
            'mimosa' => '39',
            'musgo' => '130',
            'musgo de carvalho' => '130',
            'nagarmota ou óleo de cipriol' => '374',
            'narciso' => '18',
            'néroli tunisiano' => '27',
            'notas amadeiradas' => '195',
            'notas atalcadas' => '52',
            'notas doces' => '850',
            'notas especiadas' => '1036',
            'notas herbais' => '279',
            'notas solares' => '183',
            'notas verdes' => '62',
            'noz-moscada' => '94',
            'noz-moscada indonésia' => '94',
            'óleo de vetiver java' => '67',
            'olíbano' => '55',
            'opoponax' => '95',
            'orquídea' => '30',
            'osmanthus' => '10',
            'patchouli' => '48',
            'peônia' => '36',
            'peônia vermelha' => '36',
            'pera' => '118',
            'pêssego' => '117',
            'pêssego branco' => '117',
            'petitgrain' => '64',
            'pimenta' => '158',
            'pimenta branca' => '158',
            'pimenta de szechuan' => '158',
            'pimenta preta' => '158',
            'pimenta rosa' => '90',
            'pimentão verde' => '373',
            'pitaia' => '1230',
            'pomarose' => '257',
            'pralinê' => '187',
            'raíz de orris' => '24',
            'romã' => '170',
            'rosa' => '1',
            'rosa búlgara' => '1',
            'rosa de maio' => '1',
            'rosa turca' => '1',
            'rosyfolia' => '257',
            'ruibarbo' => '300',
            'sálvia' => '174',
            'sálvia esclaréia' => '159',
            'sândalo' => '6',
            'sementes de cenoura' => '340',
            'tabaco' => '146',
            'tâmaras' => '1043',
            'tangerina' => '152',
            'tangerina verde' => '152',
            'toffee' => '248',
            'tomilho' => '270',
            'toranja' => '106',
            'tuberosa' => '25',
            'tuberosa indiana' => '25',
            'vagem de cacau' => '135',
            'verbena' => '65',
            'vetiver' => '67',
            'vetiver do haiti' => '67',
            'violeta' => '23',
            'ylang ylang' => '22',
            'zimbro' => '292',
            'bala de morango efervescente' => '311',
            's\'mores de morango' => '311',
        ];
    }
}
