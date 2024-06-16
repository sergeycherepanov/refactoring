<?php

declare(strict_types=1);

namespace App\Command;

use App\Commission;
use App\Dto\Line;
use App\Exchange\Provider\Exchangeratesapi\Exchangeratesapi;
use App\Lookup\Lookup;
use App\Lookup\Provider\Binlist\Binlist;
use Brick\Money\CurrencyConverter;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CalculateCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('calculate');
        $this->setDescription('Commission calculation for already made transactions.');
        $this->addArgument('file', InputArgument::REQUIRED, 'Path to the file with transactions.');
    }

    /**
     * Here all logic happens.
     *
     * @throws FilesystemException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $commission = new Commission(
            new Lookup(
                new Binlist(rtrim(
                    $_SERVER['API_LOOKUP_BINLIST_URL'],
                    '/'
                ) . '/')
            ),
            new CurrencyConverter(new Exchangeratesapi(
                rtrim($_SERVER['API_EXCHANGERATESAPI_URL'], '/') . '/',
                $_SERVER['API_EXCHANGERATESAPI_KEY']
            ))
        );
        $stream = $this->getStream($input->getArgument('file'));
        while (!feof($stream)) {
            try {
                $lineStr = fgets($stream);
                if (false !== $lineStr) {
                    $line = Line::fromArray(json_decode($lineStr, true, 512, JSON_THROW_ON_ERROR));
                    if (null === $line->bin || null === $line->amount || null === $line->currency) {
                        throw new \InvalidArgumentException('Invalid line: ' . $lineStr);
                    }

                    $output->writeln((string) $commission->calculate($line->bin, $line->amount, $line->currency));
                }
            } catch (\Throwable $e) {
                $output->writeln('<error>' . $e->getMessage() . '</error>');
            }
        }

        fclose($stream);

        return self::SUCCESS;
    }

    /**
     * @throws FilesystemException
     */
    private function getStream(string $filePath): mixed
    {
        $path = dirname($filePath);
        $filePath = basename($filePath);
        $adapter = new LocalFilesystemAdapter($path);
        $filesystem = new Filesystem($adapter);

        if ($filesystem->has($filePath)) {
            $stream = $filesystem->readStream($filePath);

            if (is_resource($stream)) {
                return $stream;
            }
        }

        throw new \InvalidArgumentException('File does not exist');
    }
}
