<?php

namespace App\Command;

use RASA\NLU\Model\Entity;
use RASA\NLU\Model\Intent;
use RASA\NLU\Model\ParseResponse;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Webmozart\Assert\Assert;

/**
 * @author Maximilian Berghoff <Maximilian.Berghoff@mayflower.de>
 */
class ParseFileCommand extends CommonCommand
{
    public function configure()
    {
        $this
            ->setName('rasa:nlu:parse-file')
            ->setDescription('Parse a given text for its intents.')
            ->addOption('file', 'f', InputOption::VALUE_REQUIRED, 'File with text to parse')
            ->addOption('project', 'p', InputOption::VALUE_REQUIRED, 'Project to use for the parsing request');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getOption('file');
        $project = $input->getOption('project');
        if (!\file_exists($file)) {
            $this->io->error('File does not exist');
            return;
        }
        if (!\is_readable($file)) {
            $this->io->error('File does not readable');
            return;
        }
        $text = \file_get_contents($file);
        try {
            Assert::notEmpty($text);
        } catch (\InvalidArgumentException $e) {
           $this->io->error('It makes no sense to parse an empty string.');
            return;
        }

        $response = $this->endpoints->getParsingEndpoint($project)->parseString($text);
        if (!$response instanceof ParseResponse) {
            $this->io->error('Got wrong response');
            return;
        }

        if (!$response->getIntent() instanceof  Intent) {
            $this->io->error('No Intent given.');
        } else {
            $this->io->title('Intent: '.$response->getIntent()->getName().' - Confidence: '.$response->getIntent()->getConfidence());
        }
        $originalText = $response->getText();
        $this->io->writeln('Entities found:');
        $foundEntities = [];
        if (is_array($response->getEntities())) {
            foreach ($response->getEntities() as $entity) {
                $start = $entity->getStart() - 50;
                $length = $entity->getEnd() - $entity->getStart() + 50;
                $length = $start + $length > strlen($originalText) ? strlen($originalText) - $start : $length;
                $start = $start < 0 ? 0 : $start;

                $subString = \substr($originalText, $start, $length);
                $stringEntity = \substr($originalText, $entity->getStart(), $entity->getEnd() - $entity->getStart());

                $foundEntities[] = \str_replace($stringEntity, '<fg=white;bg=red>'.$stringEntity.'</fg=white;bg=red>', $subString);
            }

            $this->io->table(['Name', 'Value', 'start', 'end', 'extractor', 'confidence'], array_map(function (Entity $entity) {
                return [$entity->getName(), $entity->getValue(), $entity->getStart(), $entity->getEnd(), $entity->getExtractor(), $entity->getConfidence()];
            }, $response->getEntities()));
        }
        $this->io->writeln('');

        $this->io->writeln('Ranking:');
        if (is_array($response->getIntentRanking())) {
            $this->io->table(['Pos.', 'Name', 'Confidence'], array_map(function (Intent $intent) {
                return [$intent->getName(), $intent->getConfidence()];
            }, $response->getIntentRanking()));
        }
        $this->io->writeln('');

        $this->io->writeln('MODEL: '.$response->getModel());
        $this->io->writeln('PROJECT: '.$response->getProjectKey());

        $this->io->writeln('');
        $this->io->listing($foundEntities);

    }
}
