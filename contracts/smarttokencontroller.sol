pragma solidity ^0.4.12;
import './tokenholder.sol';
import './lib/if/token/ifsmarttoken.sol';

contract SmartTokenController is TokenHolder {
    IFSmartToken public token;

    function SmartTokenController(IFSmartToken _token)
        validAddress(_token)
    {
        token = _token;
    }

    modifier active() {
        assert(token.owner() == address(this));
        _;
    }

    modifier inactive() {
        assert(token.owner() != address(this));
        _;
    }

    function transferTokenOwnership(address _newOwner) public ownerOnly {
        token.transferOwnership(_newOwner);
    }

    function acceptTokenOwnership() public ownerOnly {
        token.acceptOwnership();
    }

    function disableTokenTransfers(bool _disable) public ownerOnly {
        token.disableTransfers(_disable);
    }

    function withdrawFromToken(IFERC20Token _token, address _to, uint256 _amount) public ownerOnly {
        IFTokenHolder(token).withdrawTokens(_token, _to, _amount);
    }
}