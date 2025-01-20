<?php

namespace App\Console\Commands;

use App\Models\NcmCode;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ImportNcmData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ncm:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importar dados de NCM do arquivo JSON para o banco de dados';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $filePath = storage_path('app/Tabela_NCM_Vigente_20250117.json');

        if (!File::exists($filePath)) {
            $this->error('Arquivo JSON não encontrado em: ' . $filePath);
            return;
        }

        $jsonData = json_decode(File::get($filePath), true);

        if (!$jsonData || !isset($jsonData['Nomenclaturas'])) {
            $this->error('Formato do JSON inválido.');
            return;
        }

        $this->info('Importando dados do JSON...');

        foreach ($jsonData['Nomenclaturas'] as $ncm) {
            try{

                $new = NcmCode::where([
                    ['ncm_code', '=', $ncm['Codigo']]
                ])->first();

                if(!$new){
                    $new = new NcmCode();
                    $new->created_at = date('Y-m-d H:i:s');
                }

                $new->ncm_code = $ncm['Codigo'];
                $new->description = $ncm['Descricao'];
                $new->start_date = date('Y-m-d', strtotime($ncm['Data_Inicio']));
                $new->end_date = date('Y-m-d', strtotime($ncm['Data_Fim']));
                $new->normative_act_type = $ncm['Tipo_Ato_Ini'];
                $new->normative_act_number = $ncm['Numero_Ato_Ini'];
                $new->normative_act_year = $ncm['Ano_Ato_Ini'];
                $new->updated_at = date('Y-m-d H:i:s');
                $new->save();

            }catch(\Exception $e){
                dd('Erro ao tentar fazer importação!', $e);
            }
        }

        $this->info('Importação concluída com sucesso!');
    }
}
