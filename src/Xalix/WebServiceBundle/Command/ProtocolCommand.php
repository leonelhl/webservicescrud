<?php

namespace Xalix\WebServiceBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Command\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Xalix\WebServiceBundle\Util\UpdateRest;

/**
 * Description of ProtocolCommand
 *
 * @author Leox
 */
class ProtocolCommand extends ContainerAwareCommand {

    protected function configure() {
        $this->setName('webservices:protocol')
                ->setDefinition(array(new InputArgument(
                            'ProtocolName', InputArgument::REQUIRED, 'Name of the Protocol'
                    ), new InputOption(
                            'action', null, InputOption::VALUE_REQUIRED, 'Action to perform: able or unable', 'able'
                    ),))
                ->setDescription('Able or Unable a protocol')
                ->setHelp(<<<EOT
The command <info>webservices:protocol</info> active or disable a protocol. For this you can specify the option <info>--action="able"</info> or <info>--action="unable"</info>
EOT
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $container = $this->getContainer();
        $em = $container->get('doctrine')->getManager();
        $dir = $container->getParameter('dir');

        $protocol = $input->getArgument('ProtocolName');
        $action = $input->getOption('action');
        switch ($protocol) {
            case 'soap':
                switch ($action) {
                    case 'able':
                        $output->writeln('Activing SOAP protocol...');
                        $entity = $em->getRepository('WebServiceBundle:Protocol')
                                ->findOneBy(array('name' => 'SOAP'));
                        $entity->setIsActive(true);
                        $em->persist($entity);
                        $em->flush();
                        $output->writeln('SOAP protocol is able.');
                        break;
                    case 'unable':
                        $output->writeln('Deactivating SOAP protocol...');
                        $entity = $em->getRepository('WebServiceBundle:Protocol')
                                ->findOneBy(array('name' => 'SOAP'));
                        $entity->setIsActive(false);
                        $em->persist($entity);
                        $em->flush();
                        $output->writeln('SOAP protocol is unable.');
                        break;
                    default:
                        throw new \InvalidArgumentException($action . ' is not option available. The options available are "able" or "unable".');
                        break;
                }
                break;
            case 'rest':
                switch ($action) {
                    case 'able':
                        $output->writeln('Activing REST protocol...');
                        $entity = $em->getRepository('WebServiceBundle:Protocol')
                                ->findOneBy(array('name' => 'REST'));
                        $entity->setIsActive(true);
                        $em->persist($entity);
                        $em->flush();
                        $rest = new UpdateRest();
                        $rest->updatePublishRestServices($em,$dir);
                        $output->writeln('REST protocol is able.');
                        break;
                    case 'unable':
                        $output->writeln('Deactivating REST protocol...');
                        $entity = $em->getRepository('WebServiceBundle:Protocol')
                                ->findOneBy(array('name' => 'REST'));
                        $entity->setIsActive(false);
                        $em->persist($entity);
                        $em->flush();
                        $rest = new UpdateRest();
                        $rest->updatePublishRestServices($em,$dir);
                        $output->writeln('REST protocol is unable.');
                        break;
                    default:
                        throw new \InvalidArgumentException($action . ' is not option available. The options available are "able" or "unable".');
                        break;
                }
                break;
            default:
                throw new \InvalidArgumentException($protocol . ' is not argument available. The arguments available are "soap" or "rest".');
                break;
        }
    }

}

?>
