<?php
namespace Contato;

// import Contato\Model
use Contato\Model\Contato,
    Contato\Model\ContatoTable;
 
// import Zend\Db
use Zend\Db\ResultSet\ResultSet,
    Zend\Db\TableGateway\TableGateway;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    
    /**
     * Register View Helper
     */
    public function getViewHelperConfig()
    {
        return array(
            # registrar View Helper com injecao de dependecia
            'factories' => array(
                'menuAtivo'  => function($sm) {
                    return new View\Helper\MenuAtivo($sm->getServiceLocator()->get('Request'));
                },
                'message' => function($sm) {
                    return new View\Helper\Message($sm->getServiceLocator()->get('ControllerPluginManager')->get('flashmessenger'));
                },
            ),
            'invokables' => array(
                'filter' => 'Contato\View\Helper\ContatoFilter'
            ),
        );
    }
    
    /**
    * Register Services
    */
   public function getServiceConfig()
   {
       return array(
           'factories' => array(
               'ContatoTableGateway' => function ($sm) {
                   // obter adapter db atraves do service manager
                   //$adapter = $sm->get('AdapterDb');
                   $adapter = $sm->get('Zend\Db\Adapter\Adapter');

                   // configurar ResultSet com nosso model Contato
                   $resultSetPrototype = new ResultSet();
                   $resultSetPrototype->setArrayObjectPrototype(new Contato());

                   // return TableGateway configurado para nosso model Contato
                   return new TableGateway('contatos', $adapter, null, $resultSetPrototype);
               },
                'ModelContato' => function($sm){
                   // return instância model ContatoTable
                   return new ContatoTable($sm->get('ContatoTableGateway'));
                },
           )
       );
   }
}
