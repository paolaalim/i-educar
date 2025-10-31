<?php

namespace Tests\Unit\Modules\Avaliacao\Service\Boletim;

use PHPUnit\Framework\TestCase;

// Carrega dependências legadas
require_once __DIR__ . '/../../../../../../ieducar/modules/RegraAvaliacao/Model/Regra.php';
require_once __DIR__ . '/../../../../../../ieducar/modules/Avaliacao/Service/Boletim/RegraAvaliacao.php';

/**
 * Classe 'dublê' (stub) para permitir o teste do Trait.
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
 * Testa o Trait Avaliacao_Service_Boletim_RegraAvaliacao
 */
class RegraAvaliacaoTest extends TestCase
{
    /*******************************************************
     * TESTES MÉTODO 1: getRegraAvaliacaoAprovarPelaFrequenciaAposExame()
     * (Critério MC/DC)
     *******************************************************/

    /**
     * @test
     * CT1 (MC/DC 1/3): (A=V, B=V) -> V
     */
    public function testGetRegraAvaliacaoAprovarPelaFrequenciaAposExame_CT1_RetornaVerdadeiro()
    {
        $regraMock = $this->createMock(\RegraAvaliacao_Model_Regra::class);
        $regraMock->method('get')
            ->willReturnMap([
                ['aprovarPelaFrequenciaAposExame', true],
                ['formulaRecuperacao', 123] 
            ]);
        
        $service = new TraitTestClass($regraMock);
        
        $this->assertTrue($service->getRegraAvaliacaoAprovarPelaFrequenciaAposExame());
    }

    /**
     * @test
     * CT2 (MC/DC 2/3): (A=V, B=F) -> F
     */
    public function testGetRegraAvaliacaoAprovarPelaFrequenciaAposExame_CT2_RetornaFalsoSemFormula()
    {
        $regraMock = $this->createMock(\RegraAvaliacao_Model_Regra::class);
        $regraMock->method('get')
            ->willReturnMap([
                ['aprovarPelaFrequenciaAposExame', true],
                ['formulaRecuperacao', null] 
            ]);

        $service = new TraitTestClass($regraMock);
        
        $this->assertFalse($service->getRegraAvaliacaoAprovarPelaFrequenciaAposExame());
    }

    /**
     * @test
     * CT3 (MC/DC 3/3): (A=F, B=V) -> F
     */
    public function testGetRegraAvaliacaoAprovarPelaFrequenciaAposExame_CT3_RetornaFalsoRegraDesabilitada()
    {
        $regraMock = $this->createMock(\RegraAvaliacao_Model_Regra::class);
        $regraMock->method('get')
            ->willReturnMap([
                ['aprovarPelaFrequenciaAposExame', false],
                ['formulaRecuperacao', 123] 
            ]);

        $service = new TraitTestClass($regraMock);
        
        $this->assertFalse($service->getRegraAvaliacaoAprovarPelaFrequenciaAposExame());
    }

    
    /*******************************************************
     * TESTES MÉTODO 2: codigoDisciplinasAglutinadas
     * (Critério: Duas Decisões)
     *******************************************************/

    /**
     * @test
     * CT4: Cobre o caminho onde a variável já foi setada (Decisão 1 = F)
     */
    public function testCodigoDisciplinasAglutinadas_CT4_JaSetado()
    {
        $regraMock = $this->createMock(\RegraAvaliacao_Model_Regra::class);
        $service = new TraitTestClass($regraMock);
        
        $service->setCodigoDisciplinasAglutinadas(['99']);
        $resultado = $service->codigoDisciplinasAglutinadas();

        $this->assertEquals(['99'], $resultado);
    }

    /**
     * @test
     * CT5: Cobre o caminho D1=V, D2=V (Não setado, regra vazia)
     */
    public function testCodigoDisciplinasAglutinadas_CT5_NaoSetado_RegraVazia()
    {
        $regraMock = $this->createMock(\RegraAvaliacao_Model_Regra::class);

        $regraMock->method('__isset')
                  ->with('disciplinasAglutinadas')
                  ->willReturn(true);
        $regraMock->method('__get')
                  ->with('disciplinasAglutinadas')
                  ->willReturn(''); 

        $service = new TraitTestClass($regraMock);
        $resultado = $service->codigoDisciplinasAglutinadas();

        $this->assertEquals([], $resultado);
    }

    /**
     * @test
     * CT6: Cobre o caminho D1=V, D2=F (Não setado, regra com valores)
     */
    public function testCodigoDisciplinasAglutinadas_CT6_NaoSetado_RegraComValores()
    {
        $regraMock = $this->createMock(\RegraAvaliacao_Model_Regra::class);
        
        $regraMock->method('__isset')
                  ->with('disciplinasAglutinadas')
                  ->willReturn(true);
        $regraMock->method('__get')
                  ->with('disciplinasAglutinadas')
                  ->willReturn('10,20,30'); 

        $service = new TraitTestClass($regraMock);
        $resultado = $service->codigoDisciplinasAglutinadas();

        $this->assertEquals(['10', '20', '30'], $resultado);
    }
}
