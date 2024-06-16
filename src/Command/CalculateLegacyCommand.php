<?php

declare(strict_types=0);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @deprecated
 */
final class CalculateLegacyCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('calculate-legacy');
        $this->setDescription('Legacy commission calculation for already made transactions.');
        $this->addArgument('file', InputArgument::REQUIRED, 'Path to the file with transactions.');
    }

    /**
     * Here all logic happens
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = $input->getArgument('file');
        if (!is_string($file)) {
            throw new \InvalidArgumentException('File path must be a string');
        }

        if (is_file($file) === false) {
            throw new \InvalidArgumentException('File does not  exist');
        }

        foreach (explode("\n", file_get_contents($file)) as $row) {
            if (empty($row)) break;
            $p = explode(",", $row);
            $p2 = explode(':', $p[0]);
            $value[0] = trim($p2[1], '"');
            $p2 = explode(':', $p[1]);
            $value[1] = trim($p2[1], '"');
            $p2 = explode(':', $p[2]);
            $value[2] = trim($p2[1], '"}');

            $binResults = file_get_contents(rtrim($_SERVER['API_LOOKUP_BINLIST_URL'], '/') . '/' . $value[0]);
            if (!$binResults)
                die('error!');
            $r = json_decode($binResults, false);
            $isEu = $this->isEu($r->country->alpha2);

            $rate = @json_decode(file_get_contents(rtrim($_SERVER['API_EXCHANGERATESAPI_URL'], '/').'/latest?access_key='.$_SERVER['API_EXCHANGERATESAPI_KEY']), true, 512, JSON_THROW_ON_ERROR)['rates'][$value[2]];

            if ($value[2] == 'EUR' or $rate == 0) {
                $amntFixed = $value[1];
            }
            if ($value[2] != 'EUR' or $rate > 0) {
                $amntFixed = $value[1] / $rate;
            }

            echo $amntFixed * ($isEu == 'yes' ? 0.01 : 0.02);
            print "\n";
        }

        return self::SUCCESS;
    }

    private function isEu($c)
    {
        $result = false;
        switch ($c) {
            case 'AT':
            case 'BE':
            case 'BG':
            case 'CY':
            case 'CZ':
            case 'DE':
            case 'DK':
            case 'EE':
            case 'ES':
            case 'FI':
            case 'FR':
            case 'GR':
            case 'HR':
            case 'HU':
            case 'IE':
            case 'IT':
            case 'LT':
            case 'LU':
            case 'LV':
            case 'MT':
            case 'NL':
            case 'PO':
            case 'PT':
            case 'RO':
            case 'SE':
            case 'SI':
            case 'SK':
                $result = 'yes';
                return $result;
            default:
                $result = 'no';
        }

        return $result;
    }
}
