<?php
/*
 * @copyright   Copyright (C) 2015 Gamuza Technologies (http://www.gamuza.com.br/)
 * @author     Eneias Ramos de Melo <eneias@gamuza.com.br>
 */

$installer = new Mage_Catalog_Model_Resource_Setup();
$installer->startSetup();

if (!$installer->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'pagarme_subscription_plan', 'attribute_id'))
{
    $installer->addAttribute(
        Mage_Catalog_Model_Product::ENTITY,
        'pagarme_subscription_plan',
        array(
            'type'                    => 'int',
            'input'                   => 'select',
            'backend'                 => '',
            'frontend'                => '',
            'label'                   => 'Plano da Pagarme',
            'class'                   => '',
            'source'                  => 'pagarme/product_attribute_plan',
            'global'                  => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
            'visible'                 => true,
            'required'                => false,
            'user_defined'            => false,
            'default'                 => '',
            'searchable'              => false,
            'filterable'              => false,
            'comparable'              => false,
            'visible_on_front'        => false,
            'unique'                  => false,
            'apply_to'                => '',
            'is_configurable'         => false,
            'used_in_product_listing' => true,
            'option'                  => array(
                'values' => array(),
            ),
        )
    );

    $attributeId = $installer->getAttributeId(
        Mage_Catalog_Model_Product::ENTITY,
        'pagarme_subscription_plan'
    );

    $defaultSetId = $installer->getAttributeSetId(Mage_Catalog_Model_Product::ENTITY, 'default');

    $installer->addAttributeGroup(
        Mage_Catalog_Model_Product::ENTITY,
        $defaultSetId,
        'Pagarme'
    );

    //find out the id of the new group
    $groupId = $installer->getAttributeGroup(
        Mage_Catalog_Model_Product::ENTITY,
        $defaultSetId,
        'Pagarme',
        'attribute_group_id'
    );

    //assign the attribute to the group and set
    if ($attributeId > 0)
    {
        $installer->addAttributeToSet(
            Mage_Catalog_Model_Product::ENTITY,
            $defaultSetId,
            $groupId,
            $attributeId
        );
    }

    $attributes = array(
        'price',
        'special_price',
        'special_from_date',
        'special_to_date',
        'minimal_price',
        'cost',
        'tier_price',
        'weight',
        'tax_class_id',
    );

    foreach ($attributes as $attributeCode) {
        $applyTo = explode(
            ',',
            $installer->getAttribute(
                Mage_Catalog_Model_Product::ENTITY,
                $attributeCode,
                'apply_to'
            )
        );

        if (! in_array('subscription', $applyTo)) {
            $applyTo[] = 'subscription';
            $installer->updateAttribute(
                Mage_Catalog_Model_Product::ENTITY,
                $attributeCode,
                'apply_to',
                join(',', $applyTo)
            );
        }
    }
}

function addSubscriptionPlans ($installer)
{
    $table = $installer->getTable ('pagarme_subscription_plans');
    
    $sqlBlock = <<< SQLBLOCK
CREATE TABLE IF NOT EXISTS {$table}
(
    id int(11) unsigned NOT NULL AUTO_INCREMENT,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Pagarme Subscription Plans';
SQLBLOCK;

    $installer->run ($sqlBlock);

    $installer->getConnection ()
        ->addColumn ($table, 'remote_id', array(
            'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'length' => 255,
            'nullable' => false,
            'comment' => 'Remote ID',
        ));
    $installer->getConnection ()
        ->addColumn ($table, 'name', array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length' => 255,
            'nullable' => false,
            'comment' => 'Name',
        ));
    $installer->getConnection ()
        ->addColumn ($table, 'amount', array(
            'type' => Varien_Db_Ddl_Table::TYPE_FLOAT,
            'unsigned' => false,
            'nullable' => false,
            'comment' => 'Amount'
        ));
    $installer->getConnection ()
        ->addColumn ($table, 'days', array(
            'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'unsigned' => false,
            'nullable' => false,
            'comment' => 'Days',
        ));
    $installer->getConnection ()
        ->addColumn ($table, 'trial_days', array(
            'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'unsigned' => false,
            'nullable' => true,
            'comment' => 'Trial Days',
        ));
    $installer->getConnection ()
        ->addColumn ($table, 'payment_methods', array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length' => 255,
            'nullable' => true,
            'comment' => 'Payment Methods',
        ));
    $installer->getConnection ()
        ->addColumn ($table, 'charges', array(
            'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'unsigned' => false,
            'nullable' => true,
            'comment' => 'Charges',
        ));
    $installer->getConnection ()
        ->addColumn ($table, 'installments', array(
            'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'length' => 255,
            'nullable' => false,
            'comment' => 'Installments',
        ));
    $installer->getConnection ()
        ->addColumn ($table, 'is_active', array(
            'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
            'unsigned' => true,
            'nullable' => true,
            'comment' => 'Is Active',
        ));

}

function addSubscriptionTransactions ($installer)
{
    $table = $installer->getTable ('pagarme_subscription_transactions');
    
    $sqlBlock = <<< SQLBLOCK
CREATE TABLE IF NOT EXISTS {$table}
(
    id int(11) unsigned NOT NULL AUTO_INCREMENT,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='Pagarme Subscription Transactions';
SQLBLOCK;

    $installer->run ($sqlBlock);

    $installer->getConnection ()
        ->addColumn ($table, 'remote_id', array(
            'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'length' => 255,
            'nullable' => false,
            'comment' => 'Remote Id',
        ));
    $installer->getConnection ()
        ->addColumn ($table, 'order_id', array(
            'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'length' => 255,
            'nullable' => false,
            'comment' => 'Order ID',
        ));
    $installer->getConnection ()
        ->addColumn ($table, 'payment_method', array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length' => 255,
            'nullable' => false,
            'comment' => 'Payment Method',
        ));
    $installer->getConnection ()
        ->addColumn ($table, 'amount', array(
            'type' => Varien_Db_Ddl_Table::TYPE_FLOAT,
            'unsigned' => false,
            'nullable' => false,
            'comment' => 'Amount'
        ));
    $installer->getConnection ()
        ->addColumn ($table, 'installments', array(
            'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'unsigned' => false,
            'nullable' => false,
            'comment' => 'Installments',
        ));
    $installer->getConnection ()
        ->addColumn ($table, 'cost', array(
            'type' => Varien_Db_Ddl_Table::TYPE_FLOAT,
            'unsigned' => false,
            'nullable' => false,
            'comment' => 'cost',
        ));
    $installer->getConnection ()
        ->addColumn ($table, 'remote_ip', array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length' => 16,
            'nullable' => true,
            'comment' => 'Remote IP',
        ));
    $installer->getConnection ()
        ->addColumn ($table, 'authorization_code', array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'unsigned' => false,
            'nullable' => false,
            'comment' => 'Authorization Code',
        ));
    $installer->getConnection ()
        ->addColumn ($table, 'tid', array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length' => 255,
            'nullable' => false,
            'comment' => 'TID',
        ));
    $installer->getConnection ()
        ->addColumn ($table, 'boleto_url', array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            // 'length' => 255,
            'nullable' => true,
            'comment' => 'Boleto URL',
        ));
    $installer->getConnection ()
        ->addColumn ($table, 'boleto_barcode', array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            // 'length' => 255,
            'nullable' => true,
            'comment' => 'Boleto Barcode',
        ));
    $installer->getConnection ()
        ->addColumn ($table, 'boleto_expiration_date', array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            // 'length' => 255,
            'nullable' => true,
            'comment' => 'Boleto Expiration Date',
        ));
    $installer->getConnection ()
        ->addColumn ($table, 'status', array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length' => 255,
            'nullable' => false,
            'comment' => 'Status',
        ));
    $installer->getConnection ()
        ->addColumn ($table, 'created_at', array(
            'type' => Varien_Db_Ddl_Table::TYPE_DATE,
            'nullable' => false,
            'comment' => 'Created At',
        ));
    $installer->getConnection ()
        ->addColumn ($table, 'updated_at', array(
            'type' => Varien_Db_Ddl_Table::TYPE_DATE,
            'nullable' => false,
            'comment' => 'Updated At',
        ));
}

addSubscriptionPlans ($installer);
addSubscriptionTransactions ($installer);

$installer->endSetup();

