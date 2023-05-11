<?php

use App\Http\Controllers\Atendente\AtendenteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Cliente\ClienteController;
use App\Http\Controllers\Core\EmpresaController;
use App\Http\Controllers\Core\FuncaoController;
use App\Http\Controllers\Core\LinhaController;
use App\Http\Controllers\Core\MarcaController;
use App\Http\Controllers\Core\RevendaController;
use App\Http\Controllers\Core\SeguradoraController;
use App\Http\Controllers\Core\UserController;
use App\Http\Controllers\Evento\EventoController;
use App\Http\Controllers\Evento\MotivoEventoController;
use App\Http\Controllers\Localidade\LocalidadeController;
use App\Http\Controllers\Orcamento\ComprovanteController;
use App\Http\Controllers\Orcamento\OrcamentoController;
use App\Http\Controllers\OrdemServico\ArquivoController;
use App\Http\Controllers\OrdemServico\OrdemServicoController;
use App\Http\Controllers\OrdemServico\ProdutoController;
use App\Http\Controllers\OrdemServico\TimeLineController;
use App\Http\Controllers\PrestadorServico\AgendamentoController;
use App\Http\Controllers\PrestadorServico\PrestadorServicoController;

//logar no sistema
Route::post('prelogin', [LoginController::class, 'prelogin'])->name('auth.prelogin');
Route::post('login', [LoginController::class, 'login'])->name('auth.login');

//anexar arquivos
Route::get('getDados/{id}', [OrdemServicoController::class, 'getDados'])->name('api.ordem_servico.getDados');
Route::post('anexar/arquivos/{id}', [ArquivoController::class, 'anexarArquivos'])->name('api.arquivo.anexarArquivos');
// assinatura tecnico e cliente
Route::get('/orcamento/getDados/{id}', [OrcamentoController::class, 'getDados'])->name('api.ordem_servico.getDados');
Route::post('/assinar/{id}', [OrcamentoController::class, 'assinar'])->name('api.ordem_servico.assinar');
// rota temporaria para cadastrar o primeiro users
//Route::post('register', [RegisterController::class, 'register'])->name('auth.register');

Route::middleware('auth:sanctum')->group(function() {

    Route::prefix('/empresas')->group(function() {
        Route::get('/', [EmpresaController::class, 'index'])->name('api.empresas.index');
        Route::get('/edit/{id}', [EmpresaController::class, 'edit'])->name('api.empresas.index');
        Route::post('/store', [EmpresaController::class, 'store'])->name('api.empresas.store');
        Route::put('/update/{id}', [EmpresaController::class, 'update'])->name('api.empresas.update');
        Route::delete('/delete/{id}', [EmpresaController::class, 'delete'])->name('api.empresas.delete');
        Route::get('/atendentes/{id}', [EmpresaController::class, 'listarAtendentes'])->name('api.empresas.atendentes');
        Route::get('/usuarios/{id}', [EmpresaController::class, 'listarUsuarios'])->name('api.empresas.usuarios');
        Route::get('/administradores/{id}', [EmpresaController::class, 'listarAdministradores'])->name('api.empresas.administradores');
        Route::get('/seguradoras/{id}', [EmpresaController::class, 'listarSeguradoras'])->name('api.empresas.seguradoras');
        Route::get('/revendas/{id}', [EmpresaController::class, 'listarRevendas'])->name('api.empresas.revendas');
        Route::get('/marcas/{id}', [EmpresaController::class, 'listarMarcas'])->name('api.empresas.marcas');
        Route::get('/linhas/{id}', [EmpresaController::class, 'listarLinhas'])->name('api.empresas.linhas');
        Route::get('/totalizadores_os/{id}', [EmpresaController::class, 'totalizadoresOs'])->name('api.empresas.totalizadores.totalizadores_os');
        Route::get('/totalizadores_os_atendente/{id}/{atendenteId}', [EmpresaController::class, 'totalizadoresOsAtendente'])->name('api.empresas.totalizadores.totalizadores_os_atendente');
        Route::get('/estatisticas/geral/{id}', [EmpresaController::class, 'estatisticasGeral'])->name('api.empresas.estatisticas.geral');
        Route::get('/estatisticas/atendente/{id}', [EmpresaController::class, 'estatisticasAtendente'])->name('api.empresas.estatisticas.atendente');
        Route::get('/totalizadores/atendentes/{id}', [EmpresaController::class, 'totalizadoresAtendentes'])->name('api.empresas.totalizdores.atendentes');
        Route::get('/totalizadores/atendimentos/{id}', [EmpresaController::class, 'totalizadoresAtendimentos'])->name('api.empresas.totalizadores.atendimentos');
        Route::get('/totalizadores/reclamacao/{id}', [EmpresaController::class, 'totalizadoresREC'])->name('api.empresas.totalizadores.reclamacao');
        Route::get('/totalizadores/orcamento/{id}', [EmpresaController::class, 'totalizadoresOrcamento'])->name('api.empresas.totalizadores.orcamento');
        Route::get('/totalizadores/servico/{id}', [EmpresaController::class, 'totalizadoresServico'])->name('api.empresas.totalizadores.servico');
        Route::post('/store/logo', [EmpresaController::class, 'cadastrarLogo'])->name('api.empresas.cadastrarLogo');
        Route::get('/listar/logo/{id}', [EmpresaController::class, 'listarLogo'])->name('api.empresas.listarLogo');
    });

    Route::prefix('/users')->group(function() {
        Route::get('/', [UserController::class, 'index'])->name('api.users.index');
        Route::get('/edit/{id}', [UserController::class, 'edit'])->name('api.users.index');
        Route::post('/store', [UserController::class, 'store'])->name('api.users.store');
        Route::put('/update/{id}', [UserController::class, 'update'])->name('api.users.update');
        Route::delete('/delete/{id}', [UserController::class, 'delete'])->name('api.users.delete');
        Route::get('/desativar/{id}', [UserController::class, 'desativar'])->name('api.users.desativar');
    });

    Route::prefix('/ordem_servico')->group(function() {
        Route::get('/{empresa_id}', [OrdemServicoController::class, 'index'])->name('api.ordem_servico.index');
        Route::get('/lista/{empresa_id}', [OrdemServicoController::class, 'lista'])->name('api.ordem_servico.lista');
        Route::get('/listar/atendente/{atendente_id}', [OrdemServicoController::class, 'listarAtendente'])->name('api.ordem_servico.listarAtendente');
        Route::post('/store', [OrdemServicoController::class, 'store'])->name('api.ordem_servico.store');
        Route::put('/atualizar/atendente/{id}', [OrdemServicoController::class, 'atualizarAtendente'])->name('api.ordem_servico.atualizarAtendente');
        Route::post('/filtrar', [OrdemServicoController::class, 'filtrar'])->name('api.ordem_servico.filtrar');
        Route::get('/listar/status_os', [OrdemServicoController::class, 'listarStatusOs'])->name('api.ordem_servico.listarStatusOs');
        Route::get('/lista/atendente/{atendente_id}', [OrdemServicoController::class, 'listaAtendente'])->name('api.ordem_servico.listaAtendente');
        Route::get('/view/{id}', [OrdemServicoController::class, 'viewOs'])->name('api.ordem_servico.viewOs');
        Route::get('/historico/{id}', [OrdemServicoController::class, 'historicoOs'])->name('api.ordem_servico.historicoOs');
        Route::post('/pesquisa/avancada', [OrdemServicoController::class, 'pesquisaAvancada'])->name('api.ordem_servico.pesquisaAvancada');
        Route::post('/gerar/laudo', [OrdemServicoController::class, 'gerarLaudo'])->name('api.ordem_servico.gerarLaudo');
        Route::put('/update', [OrdemServicoController::class, 'atualizarOs'])->name('api.ordem_servico.atualizarOs');
        Route::get('/time_line/{id}', [OrdemServicoController::class, 'timeLine'])->name('api.ordem_servico.timeLine');
        Route::post('/finalizar', [OrdemServicoController::class, 'finalizar'])->name('api.ordem_servico.finalizar');
        Route::post('/cancelar', [OrdemServicoController::class, 'cancelar'])->name('api.ordem_servico.cancelar');
        Route::post('/troca', [OrdemServicoController::class, 'troca'])->name('api.ordem_servico.troca');
        Route::post('/gerar/pdf', [OrdemServicoController::class, 'gerarPDF'])->name('api.ordem_servico.gerarPDF');

    });

    Route::prefix('/produto')->group(function() {
        Route::post('store', [ProdutoController::class, 'store'])->name('api.produto.store');
    });

    Route::prefix('orcamento')->group(function() {
        Route::post('/store', [OrcamentoController::class, 'store'])->name('api.orcamento.store');
        Route::get('/', [OrcamentoController::class, 'index'])->name('api.orcamento.index');
        Route::get('/edit/{id}', [OrcamentoController::class, 'edit'])->name('api.orcamento.edit');
        Route::get('/listar/{os_id}', [OrcamentoController::class, 'listarOrcamentos'])->name('api.orcamento.listarOrcamentos');
        Route::get('/listar/empresa/{empresa_id}',[OrcamentoController::class, 'listarOrcamentosPorEmpresa'])->name('api.orcamento.listarOrcamentosPorEmpresa');
        Route::post('/aprovar', [OrcamentoController::class, 'aprovarOrcamento'])->name('api.orcamento.aprovarOrcamento');
        Route::post('/negar', [OrcamentoController::class, 'negarOrcamento'])->name('api.orcamento.negarOrcamento');
        Route::post('/envio', [OrcamentoController::class, 'envioOrcamento'])->name('api.orcamento.envioOrcamento');
        Route::post('/retorno', [OrcamentoController::class, 'retornoOrcamento'])->name('api.orcamento.retornoOrcamento');
        Route::post('/realizar/troca', [OrcamentoController::class, 'realizarTroca'])->name('api.orcamento.realizarTroca');
        Route::post('/recusar', [OrcamentoController::class, 'recusar'])->name('api.orcamento.recusar');
        Route::put('/update/{id}', [OrcamentoController::class, 'update'])->name('api.orcamento.update');
        Route::post('/listar/seguradora', [OrcamentoController::class, 'listarOrcamentosSeguradora'])->name('api.orcamento.listarOrcamentosSeguradora');
        Route::post('/revisar', [OrcamentoController::class, 'revisar'])->name('api.orcamento.revisar');
    });

    Route::prefix('funcao')->group(function() {
        Route::get('/', [FuncaoController::class, 'index'])->name('api.funcao.index');
    });

    Route::prefix('linha')->group(function() {
        Route::get('/', [LinhaController::class, 'index'])->name('api.linha.index');
        Route::post('/storeEmpresa', [LinhaController::class, 'storeEmpresaLinha'])->name('api.linha.storeEmpresa');
        Route::post('/nova', [LinhaController::class, 'storeNovaLinha'])->name('api.linha.nova');
    });

    Route::prefix('marca')->group(function() {
        Route::get('/', [MarcaController::class, 'index'])->name('api.marca.index');
        Route::post('/storeEmpresa', [MarcaController::class, 'storeEmpresaMarca'])->name('api.marca.storeEmpresa');
        Route::post('/nova', [MarcaController::class, 'storeNovaMarca'])->name('api.marca.nova');
    });

    Route::prefix('revenda')->group(function() {
        Route::get('/', [RevendaController::class, 'index'])->name('api.revenda.index');
        Route::post('/storeEmpresa', [RevendaController::class, 'storeEmpresaRevenda'])->name('api.revenda.storeEmpresa');
        Route::post('/nova', [RevendaController::class, 'storeNovaRevenda'])->name('api.revenda.nova');
    });

    Route::prefix('seguradora')->group(function() {
        Route::get('/', [SeguradoraController::class, 'index'])->name('api.seguradora.index');
        Route::post('/storeEmpresa', [SeguradoraController::class, 'storeEmpresaSeguradora'])->name('api.seguradora.storeEmpresa');
        Route::post('/nova', [SeguradoraController::class, 'storeNovaSeguradora'])->name('api.seguradora.nova');
    });

    Route::prefix('localidade')->group(function() {
        Route::get('/regiao', [LocalidadeController::class, 'listarRegiao'])->name('api.localidade.listarRegiao');
        Route::get('/estado', [LocalidadeController::class, 'listarEstado'])->name('api.localidade.listarEstado');
        Route::get('/cidade/{id}', [LocalidadeController::class, 'getCidades'])->name('api.localidade.getCidades');
        Route::get('/cidades', [LocalidadeController::class, 'listarCidades'])->name('api.localidade.listarCidades');
    });
    Route::prefix('cliente')->group(function() {
        Route::post('/listar/nome', [ClienteController::class, 'getClienteNome'])->name('api.cliente.getClienteNome');
    });
    Route::prefix('motivo_evento')->group(function() {
        Route::get('/', [MotivoEventoController::class, 'index'])->name('api.motivo_evento.index');
    });
    Route::prefix('atendente')->group(function() {
        Route::get('/listar/os/{id}', [AtendenteController::class, 'listarOsAtendente'])->name('api.atendente.listarOsAtendente');
        Route::get('/totalizadores/os/{id}', [AtendenteController::class, 'totalizadoresAtendente'])->name('api.atendente.totalizadoresAtendentes');
    });
    Route::prefix('prestador_servico')->group(function () {
        Route::get('/{empresa_id}', [PrestadorServicoController::class, 'index'])->name('api.prestador_servico.index');
        Route::get('/edit/{id}', [PrestadorServicoController::class, 'edit'])->name('api.prestador_servico.index');
        Route::post('/store', [PrestadorServicoController::class, 'store'])->name('api.prestador_servico.store');
        Route::put('/update/{id}', [PrestadorServicoController::class, 'update'])->name('api.prestador_servico.update');
        Route::delete('/delete/{id}', [PrestadorServicoController::class, 'delete'])->name('api.prestador_servico.delete');
        Route::put('/status/{id}', [PrestadorServicoController::class, 'status'])->name('api.prestador_servico.status');
    });
    Route::prefix('agendamento')->group(function () {
        Route::get('/{os_id}', [AgendamentoController::class, 'index'])->name('api.agendamento.index');
        Route::get('/edit/{id}', [AgendamentoController::class, 'edit'])->name('api.agendamento.index');
        Route::post('/store', [AgendamentoController::class, 'store'])->name('api.agendamento.store');
        Route::put('/update/{id}', [AgendamentoController::class, 'update'])->name('api.agendamento.update');
        Route::delete('/delete/{id}', [AgendamentoController::class, 'delete'])->name('api.agendamento.delete');
        Route::post('/reagendar/{id}', [AgendamentoController::class, 'reagendar'])->name('api.agendamento.reagendar');
        Route::post('/transferencia/prestador', [AgendamentoController::class, 'transferenciaPrestador'])->name('api.agendamento.transferenciaPrestador');
        Route::post('/previsao/reparo', [AgendamentoController::class, 'previsaoReparo'])->name('api.agendamento.previsaoReparo');
        Route::post('/vincular/prestador', [AgendamentoController::class, 'vincularPrestador'])->name('api.agendamento.vincularPrestador');
    });
    Route::prefix('evento')->group(function() {
        Route::post('/store', [EventoController::class, 'store'])->name('api.evento.store');
    });

    Route::prefix('arquivo')->group(function() {
        Route::post('/store', [ArquivoController::class, 'store'])->name('api.arquivo.store');
        Route::get('/listar/{os_id}', [ArquivoController::class, 'listarArquivos'])->name('api.arquivo.listar');
        Route::put('/atualizar/alias/{id}', [ArquivoController::class, 'atualizarAlias'])->name('api.arquivo.atualizarAlias');
        Route::delete('/delete/{id}', [ArquivoController::class, 'deletar'])->name('api.arquivo.deletar');
    });

    Route::prefix('comprovante')->group(function() {
        Route::post('/store', [ComprovanteController::class, 'store'])->name('api.comprovante.store');
        Route::get('/listar/{orcamento_id}', [ComprovanteController::class, 'listarComprovantes'])->name('api.comprovante.listar');
        Route::put('/atualizar/alias/{id}', [ComprovanteController::class, 'atualizarAlias'])->name('api.comprovante.atualizarAlias');
    });
    Route::prefix('timeline')->group(function() {
        Route::get('/listar/{os_id}',[TimeLineController::class, 'listar'])->name('api.timeline.listar');
    });
});

Route::fallback(function(){
    return response()->json(['message' => 'Recurso não encontrado.']);
});
