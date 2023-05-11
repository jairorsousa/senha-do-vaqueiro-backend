<?php

namespace App\Http\Controllers\OrdemServico;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Evento\EventoController;
use App\Models\OrdemServico\Arquivo;
use App\Models\OrdemServico\OrdemServico;
use Illuminate\Support\Facades\DB;

class ArquivoController extends Controller
{
    protected const EVENTO_ANEXAR_ARQUIVO = 22;
    public function __construct(Arquivo $model, EventoController $eventoController)
    {
        $this->model = $model;
        $this->evento = $eventoController;
    }

    public function store(Request $request)
    {
        $nameFile = null;
        $dados = $request->all();
        $ordemServico = OrdemServico::find($dados['ordem_de_servico_id']);
        ini_set('upload_max_filesize','10M');
        // Verifica se informou o arquivo e se é válido
        if ($request->hasFile('arquivo') && $request->file('arquivo')->isValid()) {

            // Define um aleatório para o arquivo baseado no timestamps atual
            $name = uniqid(date('HisYmd'));

            // Recupera a extensão do arquivo
            $extension = $request->arquivo->extension();

            // Define finalmente o nome
            $nameFile = "{$name}.{$extension}";

            // Definindo o alias
            $alias = isset($dados['alias']) ? $dados['alias'] : $nameFile;

            // Faz o upload:
            $upload = $request->arquivo->storeAs('public', $nameFile);
            // Se tiver funcionado o arquivo foi armazenado em storage/app/public/nomedinamicoarquivo.extensao
            if(!$upload) {
                return $this->sendError('Não foi possível fazer o upload do arquivo!');
            }
            $dados['nome_arquivo'] = $nameFile;
            $dados['formato'] = $extension;
            $dados['alias'] = $alias;
            $dados['usuario_empresa_id'] = auth()->user()->usuario_empresa[0]->id;
            $response = $this->model->create($dados);
            $this->gravarEvento($ordemServico->cliente_id, $ordemServico->id, self::EVENTO_ANEXAR_ARQUIVO, 'ARQUIVO ' . $nameFile . ' ANEXADO COM SUCESSO');
            return $this->sendResponse($response);
        }
    }
    public function listarArquivos($osID)
    {
        $arquivos = $this->model->where('ordem_de_servico_id',$osID)->get();

        return $this->sendResponse($arquivos);
    }
    public function atualizarAlias(Request $request, $id)
    {
        $arquivo = $this->model->find($id);
        $newAlias = $request->alias;
        if(!$arquivo) {
            return $this->sendError($this->NAO_LOCALIZADO);
        }
        $arquivo->update([
            'alias' => $newAlias
        ]);
        return $this->sendResponse($this->SUCESSO);
    }
    public function anexarArquivos(Request $request, $hash)
    {
        $arquivos = $request->all();
        $ordemServico = OrdemServico::where(DB::raw('md5(id)'),$hash)->first();
        $osID = $ordemServico->id;
        if(!$ordemServico) {
            return $this->sendError($this->NAO_LOCALIZADO);
        }
        try {
            DB::beginTransaction();
            $nameFile = null;
            foreach($arquivos as $name => $file) {
                ini_set('upload_max_filesize','10M');
                // Verifica se informou o arquivo e se é válido
                $uploadedFile = $request->file($name);
                if ($uploadedFile && $uploadedFile->isValid()) {
                    // Define um aleatório para o arquivo baseado no timestamps atual
                    $name = uniqid(date('HisYmd'));

                    // Recupera a extensão do arquivo
                    $extension = $uploadedFile->extension();

                    // Define finalmente o nome
                    $nameFile = "{$name}.{$extension}";

                    // Faz o upload:
                    $upload = $uploadedFile->storeAs('public', $nameFile);
                    // Se tiver funcionado o arquivo foi armazenado em storage/app/public/nomedinamicoarquivo.extensao
                    if(!$upload) {
                        return $this->sendError('Não foi possível fazer o upload do arquivo!');
                    };
                    $this->model->create([
                        'ordem_de_servico_id' => $osID,
                        'nome_arquivo' => $nameFile,
                        'alias' => $nameFile,
                        'formato' => $extension,
                    ]);
                    $this->gravarEvento($ordemServico->cliente_id, $ordemServico->id, self::EVENTO_ANEXAR_ARQUIVO, 'ARQUIVO ' . $nameFile . ' ANEXADO COM SUCESSO');
                    DB::commit();
                }
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->sendError($th->getMessage());
        }
        return $this->sendResponse($this->SUCESSO);
    }
    public function salvarPDF($pdf, $osID)
    {
        try {
            $ordemServico = OrdemServico::find($osID);
            $name = uniqid(date('HisYmd'));
            $extension = 'pdf';
            $nameFile = "{$name}.{$extension}";
            $path = storage_path("app/public/{$nameFile}");
            $alias = $nameFile;
            if (file_put_contents($path, $pdf)) {
                $dados = [
                    'ordem_de_servico_id' => $osID,
                    'nome_arquivo' => $nameFile,
                    'formato' => $extension,
                    'alias' => $alias
                ];
                $dados['usuario_empresa_id'] = auth()->user()->usuario_empresa[0]->id;
                $this->model->create($dados);
                $this->gravarEvento($ordemServico->cliente_id, $ordemServico->id, self::EVENTO_ANEXAR_ARQUIVO, 'ARQUIVO ' . $nameFile . ' ANEXADO COM SUCESSO');
            }
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }
    private function gravarEvento($clienteID, $osID, $eventoID, $descricao)
    {
        $this->evento->criarEvento($clienteID, $osID, $eventoID, $descricao);
    }
    public function deletar($id)
    {
        $arquivo = $this->model->find($id);
        if(!$arquivo) {
            return $this->sendError($this->NAO_LOCALIZADO);
        }
        try {
            $arquivo->delete();
            return $this->sendResponse($this->SUCESSO);
        }catch (\Throwable $th) {
            return $this->sendError($th->getMessage());
        }
    }
}
