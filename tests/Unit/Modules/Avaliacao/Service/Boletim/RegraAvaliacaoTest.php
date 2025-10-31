<?php

namespace Tests\Unit\Modules\Avaliacao\Service\Boletim; // Namespace baseado no caminho do arquivo

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../../../../../ieducar/modules/RegraAvaliacao/Model/Regra.php';
require_once __DIR__ . '/../../../../../../ieducar/modules/Avaliacao/Service/Boletim/RegraAvaliacao.php';

/**
 * Classe 'dublê' (stub) apenas para testar o Trait.
 */
class TraitTestClass
{
    // Usa o Trait que queremos testar
    use \Avaliacao_Service_Boletim_RegraAvaliacao;

    // Construtor para injetar a regra "mockada"
    public function __construct(\RegraAvaliacao_Model_Regra $regra)
    {
        $this->_setRegra($regra);
    }

    // Este método é necessário para o teste CT4
    public function setCodigoDisciplinasAglutinadas($codigos)
    {
        $this->_codigoDisciplinasAglutinadas = $codigos;
    }
}

/**
 * Teste para o Trait Avaliacao_Service_Boletim_RegraAvaliacao
 */
class RegraAvaliacaoTest extends TestCase
{
    /*****************************************************************
     * TESTES (CT1, CT2, CT3) - MÉTODO 1: getRegraAvaliacaoAprovarPelaFrequenciaAposExame()
     *****************************************************************/

    /**
     * CT1: Testa o cenário Verdadeiro (V e V)
     * Cobertura MC/DC: Combinação 1 (VV -> V)
     */
    public function testGetRegraAvaliacaoAprovarPelaFrequenciaAposExame_CT1_RetornaVerdadeiro()
    {
        // 1. Configuração do Mock (Cenário)
        $regraMock = $this->createMock(\RegraAvaliacao_Model_Regra::class);
        $regraMock->method('get')
            ->willReturnMap([
                ['aprovarPelaFrequenciaAposExame', true],  // C1 = V
                ['formulaRecuperacao', 123] // C2 = V (qualquer valor não-nulo)
            ]);
        $service = new TraitTestClass($regraMock);
        $this->assertTrue($service->getRegraAvaliacaoAprovarPelaFrequenciaAposExame());
    }

    /**
     * CT2: Testa o cenário Falso (V e F)
     * Cobertura MC/DC: Combinação 2 (VF -> F)
     */
    public function testGetRegraAvaliacaoAprovarPelaFrequenciaAposExame_CT2_RetornaFalsoSemFormula()
    {
        // 1. Configuração do Mock (Cenário)
        $regraMock = $this->createMock(\RegraAvaliacao_Model_Regra::class);
        $regraMock->method('get')
            ->willReturnMap([
                ['aprovarPelaFrequenciaAposExame', true],  // C1 = V
                ['formulaRecuperacao', null] // C2 = F (valor nulo)
            ]);
        $service = new TraitTestClass($regraMock);
        $this->assertFalse($service->getRegraAvaliacaoAprovarPelaFrequenciaAposExame());
    }

    /**
     * CT3: Testa o cenário Falso (F e V)
     * Cobertura MC/DC: Combinação 3 (FV -> F)
     */
    public function testGetRegraAvaliacaoAprovarPelaFrequenciaAposExame_CT3_RetornaFalsoRegraDesabilitada()
    {
        // 1. Configuração do Mock (Cenário)
        $regraMock = $this->createMock(\RegraAvaliacao_Model_Regra::class);
        $regraMock->method('get')
            ->willReturnMap([
                ['aprovarPelaFrequenciaAposExame', false], // C1 = F
                ['formulaRecuperacao', 123] // C2 = V (qualquer valor não-nulo)
            ]);
        $service = new TraitTestClass($regraMock);
        $this->assertFalse($service->getRegraAvaliacaoAprovarPelaFrequenciaAposExame());
    }


    /*****************************************************************
     * TESTES (CT4, CT5, CT6) - MÉTODO 2: codigoDisciplinasAglutinadas()
     *****************************************************************/

    /**
     * CT4: Teste para Decisão 1 (linha 17) = FALSO
     * (Já foi setado, deve retornar o valor direto)
     */
    public function testCodigoDisciplinasAglutinadas_CT4_JaSetado()
    {
        // 1. Configuração do Mock (Cenário)
        $regraMock = $this->createMock(\RegraAvaliacao_Model_Regra::class);
        $service = new TraitTestClass($regraMock);
        $service->setCodigoDisciplinasAglutinadas(['99']);

        // 2. Executa (Decisão 1 na linha 17 será FALSA)
        $resultado = $service->codigoDisciplinasAglutinadas();

        // 3. Verificação
        $this->assertEquals(['99'], $resultado);
    }

    /**
     * CT5: Teste para Decisão 1 (linha 17) = VERDADEIRO
     * Decisão 2 (linha 18) = VERDADEIRO
     * (Não foi setado, e a regra está vazia)
     */
    public function testCodigoDisciplinasAglutinadas_CT5_NaoSetado_RegraVazia()
    {
        // 1. Configuração do Mock (Cenário)
        $regraMock = $this->createMock(\RegraAvaliacao_Model_Regra::class);

        // Simula __isset() para o 'empty()' funcionar
        $regraMock->method('__isset')
                  ->with('disciplinasAglutinadas')
                  ->willReturn(true);
        // Simula __get()
        $regraMock->method('__get')
                  ->with('disciplinasAglutinadas')
                  ->willReturn(''); // Faz 'empty()' ser VERDADEIRO

        // 2. Instancia a classe
        $service = new TraitTestClass($regraMock);

        // 3. Executa (Decisão 1 = V, Decisão 2 = V)
        $resultado = $service->codigoDisciplinasAglutinadas();

        // 4. Verificação
        $this->assertEquals([], $resultado);
    }

    /**
     * CT6: Teste para Decisão 1 (linha 17) = VERDADEIRO
     * Decisão 2 (linha 18) = FALSO
     * (Não foi setado, e a regra tem valores)
     */
    public function testCodigoDisciplinasAglutinadas_CT6_NaoSetado_RegraComValores()
    {
        // 1. Configuração do Mock (Cenário)
        $regraMock = $this->createMock(\RegraAvaliacao_Model_Regra::class);
        
        // Simula __isset() para o 'empty()' funcionar
        $regraMock->method('__isset')
                  ->with('disciplinasAglutinadas')
                  ->willReturn(true);
        // Simula __get()
        $regraMock->method('__get')
                  ->with('disciplinasAglutinadas')
                  ->willReturn('10,20,30'); // Faz 'empty()' ser FALSO

        // 2. Instancia a classe
        $service = new TraitTestClass($regraMock);

        // 3. Executa (Decisão 1 = V, Decisão 2 = F)
        $resultado = $service->codigoDisciplinasAglutinadas();

        // 4. Verificação
        $this->assertEquals(['10', '20', '30'], $resultado);
    }
}
