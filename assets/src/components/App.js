'use strict';

import React from 'react';

import Hands from './Hands';
import Table from './Table';
import Winner from './Winner';

class App extends React.Component
{

    constructor(props)
    {
        super(props);
        this.state = {
            error: null,
            isLoaded: false,
            hands: [],
            table: [],
            winner: []
        };
    }


    componentDidMount()
    {
        fetch("/data")
            .then(res => res.json())
            .then(
                (result) => {
                    console.log(result);
                    this.setState({
                        isLoaded: true,
                        hands: result.hands,
                        table: result.table,
                        winner: result.winner
                    });
                },
                // Note: it's important to handle errors here
                // instead of a catch() block so that we don't swallow
                // exceptions from actual bugs in components.
                (error) => {
                    this.setState({
                        isLoaded: true,
                        error
                    });
                }
            )
    }

    render()
    {
        const { error, isLoaded, hands, table, winner } = this.state;

        if (error) {
            return <div>Error: {error.message}</div>;
        } else if (!isLoaded) {
            return <div>Loading...</div>;
        } else {

            return (
                <div>
                    <Hands hands={hands} width={2} />
                    <div className={'row'}>
                        <Table table={table} />
                        <Winner winner={winner} />
                    </div>
                </div>
            );
        }
    }
}

export default App