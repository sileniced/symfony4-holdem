'use strict';

import React from 'react';
import Hands from './Hands';

const Winner = ({winner}) => (
    <div className={'col-md-6'}>
        <div className={'col-md-12'}>
            WINNER: {winner.name}
        </div>
        <Hands hands={winner.hands} width={6} />
    </div>
);

export default Winner