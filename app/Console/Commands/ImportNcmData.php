<?php

namespace App\Console\Commands;

use App\Models\NcmCode;
use App\Models\NcmCodeHistory;
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
        //$filePath = storage_path('app/Tabela_NCM_Vigente_20250117.json');
        $filePath = storage_path('app/Tabela_NCM_Vigente_20250120.json');

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

                // Se o NCM não existe no banco
                if(!$new){
                    $new = new NcmCode();
                    $new->created_at = date('Y-m-d H:i:s');
                }else{
                    // Se já está no banco

                    // Compara dados antigos com os novos
                    $changes = [];
                    $fieldList = [
                        'description',
                        'start_date',
                        'end_date',
                        'normative_act_type',
                        'normative_act_number',
                        'normative_act_year'
                    ];

                    $ptFields = [
                        'description' => 'Descricao',
                        'start_date' => 'Data_Inicio',
                        'end_date' => 'Data_Fim',
                        'normative_act_type' => 'Tipo_Ato_Ini',
                        'normative_act_number' => 'Numero_Ato_Ini',
                        'normative_act_year' => 'Ano_Ato_Ini',
                    ];

                    foreach ($fieldList as $field) {
                        if($field == 'start_date' || $field == 'end_date'){

                            $auxDateNcm = \DateTime::createFromFormat('Y-m-d', $new->$field);

                            if ($auxDateNcm->format('d/m/Y') != $ncm[$ptFields[$field]]) {
                                $changes[$field] = $new->$field;
                            }
                        }else{
                            if ($new->$field != $ncm[$ptFields[$field]]) {
                                $changes[$field] = $new->$field;
                            }
                        }


                    }

                    // Se ocorreram mudanças, salva na tabela do histórico
                    if (!empty($changes)) {

                        NcmCodeHistory::insert([
                            'ncm_code' => $new->ncm_code,
                            'description' => $changes['description'] ?? $new->description,
                            'start_date' => $changes['start_date'] ?? $new->start_date,
                            'end_date' => $changes['end_date'] ?? $new->end_date,
                            'normative_act_type' => $changes['normative_act_type'] ?? $new->normative_act_type,
                            'normative_act_number' => $changes['normative_act_number'] ?? $new->normative_act_number,
                            'normative_act_year' => $changes['normative_act_year'] ?? $new->normative_act_year,
                            'updated_at' => now(),
                            'updated_by' => 'JSON Import', // Origem das mudanças
                        ]);
                    }
                }

                $new->ncm_code = $ncm['Codigo'];
                $new->description = $ncm['Descricao'];

                $auxNcmStartDate = \DateTime::createFromFormat('d/m/Y', $ncm['Data_Inicio']);
                $new->start_date = $auxNcmStartDate->format('Y-m-d');

                $auxNcmEndDate = \DateTime::createFromFormat('d/m/Y', $ncm['Data_Fim']);
                $new->end_date = $auxNcmEndDate->format('Y-m-d');

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
    } // handle()

}
