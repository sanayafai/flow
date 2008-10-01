<?php
declare(ENCODING = 'utf-8');
namespace F3::FLOW3::Persistence;

/*                                                                        *
 * This script is part of the TYPO3 project - inspiring people to share!  *
 *                                                                        *
 * TYPO3 is free software; you can redistribute it and/or modify it under *
 * the terms of the GNU General Public License version 2 as published by  *
 * the Free Software Foundation.                                          *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General      *
 * Public License for more details.                                       *
 *                                                                        */

/**
 * @package FLOW3
 * @subpackage Persistence
 * @version $Id$
 */

require_once('Fixture/F3_FLOW3_Tests_Persistence_Fixture_Repository1.php');
require_once('Fixture/F3_FLOW3_Tests_Persistence_Fixture_Entity1.php');
require_once('Fixture/F3_FLOW3_Tests_Persistence_Fixture_ValueObject1.php');

/**
 * Testcase for the Class Schema Builder
 *
 * @package FLOW3
 * @subpackage Persistence
 * @version $Id$
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
class ClassSchemataBuilderTest extends F3::Testing::BaseTestCase {

	/**
	 * @var F3::FLOW3::Reflection::Service
	 */
	protected $reflectionService;

	/**
	 * @var F3::FLOW3::Persistence::ClassSchemataBuilder
	 */
	protected $builder;

	/**
	 * Sets up this testcase
	 *
	 * @return void
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function setUp() {
		$this->reflectionService = new F3::FLOW3::Reflection::Service();
		$this->reflectionService->initialize(
			array('F3::FLOW3::Tests::Persistence::Fixture::Entity1', 'F3::FLOW3::Tests::Persistence::Fixture::Repository1', 'F3::FLOW3::Tests::Persistence::Fixture::ValueObject1')
		);
		$this->builder = new F3::FLOW3::Persistence::ClassSchemataBuilder($this->reflectionService);
	}

	/**
	 * @test
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function classSchemaOnlyContainsNonTransientProperties() {
		$expectedProperties = array('someString', 'someInteger', 'someFloat', 'someDate', 'someBoolean', 'someIdentifier');

		$builtClassSchema = array_pop($this->builder->build(array('F3::FLOW3::Tests::Persistence::Fixture::Entity1')));
		$actualProperties = array_keys($builtClassSchema->getProperties());
		sort($expectedProperties);
		sort($actualProperties);
		$this->assertEquals($expectedProperties, $actualProperties);
	}

	/**
	 * @test
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function propertyTypesAreDetectedFromVarAnnotations() {
		$expectedProperties = array(
			'someBoolean' => 'boolean',
			'someString' => 'string',
			'someInteger' => 'integer',
			'someFloat' => 'float',
			'someDate' => 'DateTime',
			'someIdentifier' => 'string'
		);

		$builtClassSchema = array_pop($this->builder->build(array('F3::FLOW3::Tests::Persistence::Fixture::Entity1')));
		$actualProperties = $builtClassSchema->getProperties();
		asort($expectedProperties);
		asort($actualProperties);
		$this->assertEquals($expectedProperties, $actualProperties);
	}

	/**
	 * @test
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function modelTypeRepositoryIsRecognizedByRepositoryAnnotation() {
		$builtClassSchema = array_pop($this->builder->build(array('F3::FLOW3::Tests::Persistence::Fixture::Repository1')));
		$this->assertEquals($builtClassSchema->getModelType(), F3::FLOW3::Persistence::ClassSchema::MODELTYPE_REPOSITORY);
	}

	/**
	 * @test
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function modelTypeEntityIsRecognizedByValueObjectAnnotation() {
		$builtClassSchema = array_pop($this->builder->build(array('F3::FLOW3::Tests::Persistence::Fixture::Entity1')));
		$this->assertEquals($builtClassSchema->getModelType(), F3::FLOW3::Persistence::ClassSchema::MODELTYPE_ENTITY);
	}

	/**
	 * @test
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function modelTypeValueObjectIsRecognizedByValueObjectAnnotation() {
		$builtClassSchema = array_pop($this->builder->build(array('F3::FLOW3::Tests::Persistence::Fixture::ValueObject1')));
		$this->assertEquals($builtClassSchema->getModelType(), F3::FLOW3::Persistence::ClassSchema::MODELTYPE_VALUEOBJECT);
	}

	/**
	 * @test
	 * @author Robert Lemke <robert@typo3.org>
	 */
	public function classSchemaContainsNameOfItsRelatedClass() {
		$builtClassSchema = array_pop($this->builder->build(array('F3::FLOW3::Tests::Persistence::Fixture::Entity1')));
		$this->assertEquals($builtClassSchema->getClassName(), 'F3::FLOW3::Tests::Persistence::Fixture::Entity1');
	}

	/**
	 * @author Karsten Dambekalns <karsten@typo3.org>
	 * @test
	 */
	public function identifierPropertyIsDetectedFromAnnotation() {
		$builtClassSchema = array_pop($this->builder->build(array('F3::FLOW3::Tests::Persistence::Fixture::Entity1')));
		$this->assertEquals($builtClassSchema->getIdentifierProperty(), 'someIdentifier');
	}

	/**
	 * @author Karsten Dambekalns <karsten@typo3.org>
	 * @test
	 */
	public function identifierPropertyIsSetAsRegularPropertyAsWell() {
		$builtClassSchema = array_pop($this->builder->build(array('F3::FLOW3::Tests::Persistence::Fixture::Entity1')));
		$this->assertTrue(array_key_exists('someIdentifier', $builtClassSchema->getProperties()));
	}
}

?>