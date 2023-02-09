<?php

namespace App\Command;

use App\Command\Parse\ParseError;
use App\Parsing\Parser;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @AsCommand
 */
#[AsCommand(name: 'parse')]
class ParseCommand extends Command
{
    private const INPUT_FILE_ARGUMENT = 'input';
    private const OUTPUT_FILE_ARGUMENT = 'output';

    /**
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     * @throws \App\Command\Parse\ParseError
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $inputFileName = $this->inputFileName($input);
        $resultString = (new Parser($inputFileName))->run();
        $writtenTo = $this->writeResult($this->outputFileName($input), $resultString);

        $output->writeln("File Saved: {$writtenTo}");
        return Command::SUCCESS;
    }

    protected function configure(): void
    {
        $this
            ->addArgument(self::INPUT_FILE_ARGUMENT, InputArgument::REQUIRED, 'Csv file to process?')
            ->addArgument(self::OUTPUT_FILE_ARGUMENT, InputArgument::REQUIRED, 'Json file to save?')
        ;
    }

    private function inputFileName(InputInterface $input)
    {
        return $input->getArgument(self::INPUT_FILE_ARGUMENT);
    }

    private function outputFileName(InputInterface $input)
    {
        return $input->getArgument(self::OUTPUT_FILE_ARGUMENT);
    }

    /**
     * @param mixed  $outputFileName
     * @param string $resultString
     *
     * @return string
     * @throws \App\Command\Parse\ParseError
     */
    private function writeResult(mixed $outputFileName, string $resultString): string
    {
        $realFileName = \realpath($outputFileName);
        if (false === $realFileName) {
            /// Если файла не существует,
            /// формируем полное имя из директории
            $fileDir = \realpath('');
            $baseName = \basename($outputFileName);
            $realFileName = $fileDir . '/' . $baseName;
        } else {
            $fileDir = \dirname($realFileName);
        }

        try {
            if (!\is_dir($fileDir) && !\mkdir($fileDir) && !\is_dir($fileDir)) {
                throw new ParseError("Directory was not created: {$fileDir}");
            }
        } catch (ParseError $parseError) {
            throw $parseError;
        } catch (\Throwable $throwable) {
            throw new ParseError($throwable->getMessage(), $throwable->getCode(), $throwable);
        }
        \file_put_contents($realFileName, $resultString);

        return $realFileName;
    }
}