<?php
declare(strict_types=1);

namespace LizardsAndPumpkins\MagentoConnector;

interface Config
{
    /**
     * @param string $store
     * @return string
     */
    public function getLocaleFrom($store);

    /**
     * @return string
     */
    public function getLocalPathForProductExport();

    /**
     * @return string
     */
    public function getLocalFilenameTemplate();

    /**
     * @return Mage_Core_Model_Store[]
     */
    public function getStoresWithIdKeys();

    /**
     * @return string
     */
    public function getCategoryUrlSuffix();

    public function getImageTargetDirectory();

    public function getStoresToExport();
}
