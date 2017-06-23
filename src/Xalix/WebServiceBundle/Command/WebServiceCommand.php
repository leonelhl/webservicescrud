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
class WebServiceCommand extends ContainerAwareCommand {

    protected function configure() {
        $this->setName('webservices:service')
                ->setDefinition(array(new InputArgument(
                            'ServiceSlug', InputArgument::REQUIRED, 'Slug of the Web Service'
                    ), new InputOption(
                            'action', null, InputOption::VALUE_REQUIRED, 'Action to perform: able or unable', 'able'
                    ),))
                ->setDescription('Able or Unable a Web Service')
                ->setHelp(<<<EOT
The command <info>webservices:service</info> active or disable a Web Service. For this you can specify the option <info>--action="able"</info> or <info>--action="unable"</info>
EOT
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $container = $this->getContainer();
        $em = $container->get('doctrine')->getManager();
        $dir = $container->getParameter('dir');
        $service = $input->getArgument('ServiceSlug');
        $action = $input->getOption('action');

        $entity = $em->getRepository('WebServiceBundle:WebService')->findOneBy(array('slug' => $service));
        if (!$entity) {
            throw new \InvalidArgumentException('Web Service ' . $service . ' not exist.');
        }
        switch ($action) {
            case 'able':
                $output->writeln('Activing Web Service ' . $service . '...');
                $entity = $em->getRepository('WebServiceBundle:WebService')
                        ->findOneBy(array('slug' => $service));
                $entity->setIsActive(true);
                $em->persist($entity);
                $em->flush();
                if ($entity->getProtocol() == 'REST') {
                    $rest = new UpdateRest();
                    $rest->updatePublishRestServices($em, $dir);
                }
                $output->writeln('Web Service ' . $service . ' is able.');
                break;
            case 'unable':
                $output->writeln('Deactivating Web Service ' . $service . '...');
                $entity = $em->getRepository('WebServiceBundle:WebService')
                        ->findOneBy(array('slug' => $service));
                $entity->setIsActive(false);
                $em->persist($entity);
                $em->flush();
                if ($entity->getProtocol() == 'REST') {
                    $rest = new UpdateRest();
                    $rest->updatePublishRestServices($em, $dir);
                }
                $output->writeln('Web Service ' . $service . ' is unable.');
                break;
            default:
                throw new \InvalidArgumentException($action . ' is not option available. The options available are "able" or "unable".');
                break;
        }
    }

}

?>
