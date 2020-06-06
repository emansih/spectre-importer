<?php
/**
 * ManageMessages.php
 * Copyright (c) 2020 james@firefly-iii.org
 *
 * This file is part of the Firefly III Spectre importer
 * (https://github.com/firefly-iii/spectre-importer).
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);


namespace App\Console;

/**
 * Trait ManageMessages
 */
trait ManageMessages
{

    /**
     * @param string $key
     * @param array  $messages
     */
    protected function listMessages(string $key, array $messages): void
    {
        if (count($messages) > 0) {
            /**
             * @var int   $index
             * @var array $error
             */
            foreach ($messages as $index => $list) {
                /** @var string $line */
                foreach ($list as $line) {
                    $this->error(sprintf('%s in line #%d: %s', $key, $index + 1, $line));
                }
            }
        }
    }
}
