'use strict';

import React from 'react';

const Table = ({table}) => (
    <div className={'col-md-6'}>
        <ul>
            <li>pot: {table.pot}</li>
        </ul>
        {table.cards.map((card, index) => (
            <img key={index} src={ card.pngName } className={'pkr-card'} />
        ))}
    </div>
);

export default Table