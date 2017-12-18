pragma solidity ^0.4.12;
import './owned.sol';
import './utils.sol';
import './lib/if/token/iferc20token.sol';
import './lib/if/token/iftokenholder.sol';

contract TokenHolder is IFTokenHolder, Owned, Utils {

    function TokenHolder() {
    }

    function withdrawTokens(IFERC20Token _token, address _to, uint256 _amount)
        public
        ownerOnly
        validAddress(_token)
        validAddress(_to)
        notThis(_to)
    {
        assert(_token.transfer(_to, _amount));
    }
}