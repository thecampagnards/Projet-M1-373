<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

class CameraResetCommand extends ContainerAwareCommand
{
  protected function configure()
  {
      $this
      ->setName('app:camera-reset')
      ->setDescription('Reset des viewers de la caméra.');
  }

  protected function execute(InputInterface $input, OutputInterface $output)
  {
      // Showing when the script is launched
      $now = new \DateTime();
      $output->writeln('<comment>Start : ' . $now->format('d-m-Y G:i:s') . ' ---</comment>');

      $this->import($input, $output);

      // Showing when the script is over
      $now = new \DateTime();
      $output->writeln('');
      $output->writeln('<comment>End : ' . $now->format('d-m-Y G:i:s') . ' ---</comment>');
  }

  protected function import(InputInterface $input, OutputInterface $output)
  {

    // Getting doctrine manager
    $em = $this->getContainer()->get('doctrine')->getManager();
    // Turning off doctrine default logs queries for saving memory
    $em->getConnection()->getConfiguration()->setSQLLogger(null);

    $data = $this->getContainer()->get('doctrine')->getRepository('AppBundle:Camera')->findAll();

    // Define the size of record, the frequency for persisting the data and the current index of records
    $size = count($data);
    $batchSize = 20;
    $i = 1;

    // Starting progress
    $progress = new ProgressBar($output, $size);
    $progress->start();

    // Processing on each row of data
    foreach($data as $row) {

      // Do stuff here !
      $row->setViewer(0);

      // Persisting the current user
      $em->persist($row);

      // Each 20 cameras persisted we flush everything
      if (($i % $batchSize) === 0) {

        $em->flush();
        // Detaches all objects from Doctrine for memory save
        $em->clear();

        // Advancing for progress display on console
        $progress->advance($batchSize);

        $now = new \DateTime();
        $output->writeln(' of cameras reseted ... | ' . $now->format('d-m-Y G:i:s'));

      }

      $i++;

    }

    // Flushing and clear data on queue
    $em->flush();
    $em->clear();

    // Ending the progress bar process
    $progress->finish();
  }
}
