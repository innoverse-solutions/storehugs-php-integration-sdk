<?php

declare(strict_types=1);

namespace StoreHugs\Integration\Admin;

/** A quotation's pipeline moves. */
enum QuotationAction: string
{
    case Publish = 'publish';
    case Revise = 'revise';
    case Agree = 'agree';
    case AgreeAndOrder = 'agree_and_order';
    case CreateOrder = 'create_order';
    case Cancel = 'cancel';
}
